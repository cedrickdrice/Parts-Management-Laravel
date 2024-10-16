<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parts\GetTeamPartRequest;
use App\Http\Requests\Parts\UploadPartsRequest;
use App\Services\PartsService;
use Illuminate\Http\Request;
use Validator;

class PartsManagementController extends Controller
{
    protected $oPartService;

    public function __construct(PartsService $oPartService)
    {
        $this->oPartService = $oPartService;
    }

    /**
     * Retrieve Team Parts
     * @param $team_id
     * @param GetTeamPartRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTeamParts($team_id, GetTeamPartRequest $request)
    {
        $oGetTeamParts = $this->oPartService->getParts($team_id, $request);
        $bIsPartsEmpty = $oGetTeamParts['parts']->isEmpty();

        if ($bIsPartsEmpty) {
            return response()->json([
                'result' => 'success',
                'data' => $oGetTeamParts['parts']
            ]);
        }

        return response()->json([
            'result' => 'success',
            'data' => $oGetTeamParts['parts'],
            'meta' => [
                'current_page' => $oGetTeamParts['current_page'],
                'per_page' => $oGetTeamParts['per_page'],
                'total' => $oGetTeamParts['total'],
                'total_pages' => ceil($oGetTeamParts['total'] / $oGetTeamParts['per_page']),
            ]
        ]);
    }

    /**
     * Upload Parts from CSV
     * @param UploadPartsRequest $uploadPartsRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadParts(UploadPartsRequest $uploadPartsRequest)
    {
        $path = $uploadPartsRequest->file('file')->store('uploads');
        $iJobCount = $this->oPartService->uploadParts(storage_path('app/' . $path));
        return response()->json([
            'result' => 'success',
            'message' => "{$iJobCount} jobs have been dispatched for processing."
        ]);
    }

    /**
     * Link Parts to Team
     * @param $team_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function associateTeamParts($team_id, Request $request)
    {
        $this->oPartService->associateTeamParts($team_id, $request->parts);
        return response()->json([
            'result' => 'success',
            'message' => 'Parts associated successfully.'
        ]);
    }

    /**
     * Update Team Parts Pricing
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTeamPartPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv',
            'team_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv',
        ]);

        $file = $request->file('file');
        $uploadedParts = array_map('str_getcsv', file($file->getRealPath()));
        array_shift($uploadedParts);

        $count = $this->oPartService->updateTeamPartPricing($uploadedParts, $request->input('team_id'));

        return response()->json([
            'result' => 'success',
            'message' => "{$count} parts updated successfully."
        ]);
    }
}
