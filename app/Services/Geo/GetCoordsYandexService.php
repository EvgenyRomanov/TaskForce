<?php

namespace App\Services\Geo;

use Illuminate\Support\Facades\Http;

/**
 * https://snipp.ru/php/yandex-geocoder
 */
class GetCoordsYandexService implements GetCoordsInterface
{
    private string $apiKey;
    private string $url;

    public function __construct()
    {
        $this->apiKey = config('ya_geocoder_api.key');
        $this->url = config('ya_geocoder_api.url');
    }

    public function getCoorsByAddress(string $address): array
    {
        $query = [
            'apikey' => $this->apiKey,
            'geocode' => $address,
            'format' => 'json',
            'kind' => 'locality'
        ];
        $response = Http::get($this->url, $query)->json();
        $administrativeArea = $response['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea'] ?? null;
        $city = ($administrativeArea['SubAdministrativeArea']['Locality']['LocalityName'] ?? $administrativeArea['AdministrativeAreaName']) ?? null;
        $coords = $response['response']['GeoObjectCollection']['featureMember']
            [0]['GeoObject']['Point']['pos'] ?? null;

        if ($coords)
            list($lon, $lat) = explode(' ', $coords);
        else
            list($lon, $lat) = [null, null];

        return [
            $city,
            $lon,
            $lat
        ];
    }
}
