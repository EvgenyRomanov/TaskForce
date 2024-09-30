<?php

namespace App\Actions\Task\DTO;

use App\Models\Task;

readonly class RespondTaskActionDTO
{
    public function __construct(
        public Task $task,
        public ?string $comment = null,
        public ?int $budget = null
    ) {
    }
}
