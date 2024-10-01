<?php

namespace App\Services\Response\DTO;

use App\Models\Task;

readonly class AcceptResponseDTO
{
    public function __construct(
        public Task $task,
        public int $executorId,
    ) {
    }
}
