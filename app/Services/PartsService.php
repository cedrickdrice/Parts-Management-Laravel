<?php

namespace App\Services;

use App\Jobs\ImportCsvJob;
use App\Models\Part;
use App\Models\TeamPart;

class PartsService extends BaseService
{
    /**
     * Retrieve Parts
     * @param $iTeamId
     * @param $iPage
     * @param $iOffset
     * @param $iPageCount
     * @return array
     */
    public function getParts($iTeamId, $request)
    {
        $iPage = $request->input('page', 0);
        $iPageCount = $request->input('per_page', 100);
        $iOffset = ($iPage - 1) * $iPageCount;

        if (intval($iTeamId) === 0 || $iPage === 0) {
            return $this->getAllParts($iTeamId, $iPage, $iOffset, $iPageCount);
        }

        return $this->getTeamParts($iTeamId, $iPage, $iOffset, $iPageCount);
    }

    /**
     * Get Parts based on Team
     * @param $iTeamId
     * @param $iPage
     * @param $iOffset
     * @param $iPageCount
     * @return array
     */
    private function getTeamParts($iTeamId, $iPage, $iOffset, $iPageCount)
    {
        $combinedParts = Part::leftJoin('team_parts', function ($join) use ($iTeamId) {
            $join->on('parts.id', '=', 'team_parts.part_id')
                ->where('team_parts.team_id', '=', $iTeamId);
        })
            ->select(
                'parts.id as part_id',
                'parts.part_type',
                'parts.manufacturer',
                'parts.model_number',
                'parts.list_price',
                'team_parts.multiplier',
                'team_parts.team_price'
            )
            ->skip($iOffset)
            ->take($iPageCount)
            ->get();

        $totalCount = Part::leftJoin('team_parts', function ($join) use ($iTeamId) {
            $join->on('parts.id', '=', 'team_parts.part_id')
                ->where('team_parts.team_id', '=', $iTeamId);
        })->count();

        return array(
            'parts' => $combinedParts,
            'current_page' => $iPage,
            'per_page' => $iPageCount,
            'total' => $totalCount,
            'total_pages' => ceil($totalCount / $iPageCount)
        );
    }

    /**
     * Get all Parts
     * @param $iPage
     * @param $iOffset
     * @param $iPageCount
     * @return array
     */
    private function getAllParts($iTeam, $iPage, $iOffset, $iPageCount)
    {
        $combinedParts = Part::query()
            ->select([
                'parts.id as part_id',
                'parts.part_type',
                'parts.manufacturer',
                'parts.model_number',
                'parts.list_price',
                'parts.created_at',
                'parts.updated_at',
                'parts.active'
            ]);

        if ($iTeam > 0) {
            $combinedParts = Part::leftJoin('team_parts', function ($join) use ($iTeam) {
                $join->on('parts.id', '=', 'team_parts.part_id')
                    ->where('team_parts.team_id', '=', $iTeam);
            });
        }

        if ($iPage > 0) {
            $combinedParts = $combinedParts
                ->skip($iOffset)
                ->take($iPageCount);
        }

        $combinedParts = $combinedParts->get();

        $totalCount = Part::query()->count();
        return array(
            'parts' => $combinedParts,
            'current_page' => $iPage,
            'per_page' => $iPageCount,
            'total' => $totalCount,
            'total_pages' => ceil($totalCount / $iPageCount)
        );
    }

    /**
     * Create/Update parts from CSV entries
     * @param string $oUploadedCSV
     * @return array
     */
    public function uploadParts(string $oUploadedCSV)
    {
        $aUploadedParts = array_map('str_getcsv', file($oUploadedCSV));
        array_shift($aUploadedParts);
        $partsCollection = collect($aUploadedParts);

        $chunkSize = 5000;
        $jobCount = 0;

        // Dispatch Job to create
        $partsCollection->chunk($chunkSize)->each(function ($chunk) use (&$jobCount) {
            ImportCsvJob::dispatch($chunk->toArray());
            $jobCount++;
        });
        return $jobCount;
    }

    /**
     * Associate parts with a team using provided part IDs.
     * @param int $teamId
     * @param array $partIds
     * @return void
     */
    public function associateTeamParts(int $teamId, array $partIds)
    {
        foreach ($partIds as $partId) {
            $part = Part::find($partId);
            if ($part) {
                $listPrice = $part->list_price;
                $defaultMultiplier = 1;
                $defaultTeamPrice = $listPrice;

                TeamPart::updateOrCreate(
                    ['team_id' => $teamId, 'part_id' => $partId],
                    [
                        'team_price' => $defaultTeamPrice,
                        'multiplier' => $defaultMultiplier,
                        'static_price' => $defaultTeamPrice,
                    ]
                );
            }
        }
    }

    /**
     * Update team part pricing from the uploaded CSV entries.
     * @param array $uploadedParts
     * @param int $teamId
     * @return int
     */
    public function updateTeamPartPricing(array $uploadedParts, int $teamId)
    {
        $count = 0;

        foreach ($uploadedParts as $uploadedPart) {
            [$partType, $manufacturer, $modelNumber, $listPrice, $multiplier, $staticPrice] = $uploadedPart;

            $part = Part::where('model_number', $modelNumber)
                ->where('manufacturer', $manufacturer)
                ->first();

            if ($part) {
                $teamPart = TeamPart::where('part_id', $part->id)
                    ->where('team_id', $teamId)
                    ->first();

                if ($teamPart) {
                    if (!empty($multiplier)) {
                        $teamPart->multiplier = $multiplier;
                        $teamPart->team_price = $listPrice * $multiplier;
                        $teamPart->static_price = $listPrice * $multiplier;
                    } elseif (!empty($staticPrice)) {
                        $teamPart->multiplier = 1;
                        $teamPart->static_price = $staticPrice;
                        $teamPart->team_price = $staticPrice;
                    }
                    $teamPart->save();
                    $count++;
                }
            }
        }

        return $count;
    }
}
