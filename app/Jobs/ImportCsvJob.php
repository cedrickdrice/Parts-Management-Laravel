<?php

namespace App\Jobs;

use App\Models\Part;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Validator;

class ImportCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function handle()
    {
        foreach ($this->records as $record) {
            $active = $record[0];
            $partType = $record[1];
            $manufacturer = $record[2];
            $modelNumber = $record[3];
            $listPrice = $record[4];

            $bValidateEntry = $this->validatePartEntry($active, $partType, $manufacturer, $modelNumber, $listPrice);
            if ($bValidateEntry === true) {
                Part::updateOrCreate(
                    ['model_number' => $modelNumber, 'manufacturer' => $manufacturer],
                    [
                        'active' => ($active === 'Y'),
                        'part_type' => $partType,
                        'list_price' => $listPrice,
                    ]
                );
            }
        }
    }

    /**
     * Validate entries from csv
     * @param $active
     * @param $partType
     * @param $manufacturer
     * @param $modelNumber
     * @param $listPrice
     * @return bool
     */
    private function validatePartEntry($active, $partType, $manufacturer, $modelNumber, $listPrice)
    {
        $validator = Validator::make(
            [
                'active'        => $active,
                'part_type'     => $partType,
                'manufacturer'  => $manufacturer,
                'model_number'  => $modelNumber,
                'list_price'    => $listPrice,
            ],
            [
                'active'        => 'required|in:Y,N',
                'part_type'     => 'required|string',
                'manufacturer'  => 'required|string',
                'model_number'  => 'required|string',
                'list_price'    => 'required|numeric',
            ]
        );

        return $validator->fails() === false;
    }
}
