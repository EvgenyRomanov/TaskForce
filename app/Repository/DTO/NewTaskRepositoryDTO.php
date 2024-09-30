<?php

namespace App\Repository\DTO;

readonly class NewTaskRepositoryDTO
{
    public function __construct(
        public bool $remoteWork,
        public bool $withoutResponse,
        public array $categories,
        public ?int $period = null,
        public ?int $cityId = null,
    ){
    }
}
