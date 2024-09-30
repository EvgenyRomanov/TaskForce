<?php

namespace App\Services\Response\DTO;

readonly class CreateResponseDTO
{
    public function __construct(
        public int $taskId,
        public int $executorId,
        public ?string $comment = null,
        public ?int $budget = null,
    ){
    }
}
