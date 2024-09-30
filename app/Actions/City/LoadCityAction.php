<?php

namespace App\Actions\City;

use App\Helpers;
use App\Models\City;

class LoadCityAction
{
    public function __invoke(): void
    {
        $filePath = storage_path('initial_data/cities.csv');
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        $header = array_map(function ($item) {
            return Helpers::removeUTF8Bom($item);
        }, $header);

        while ($row = fgetcsv($file)) {
            $data = array_combine($header, $row);
            City::query()->create($data);
        }

        fclose($file);
    }
}
