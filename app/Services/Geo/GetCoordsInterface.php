<?php

namespace App\Services\Geo;

interface GetCoordsInterface
{
    public function getCoorsByAddress(string $address): array;
}
