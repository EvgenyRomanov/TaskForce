<?php

namespace App\Actions\Task\DTO;

use App\Models\Task;

readonly class CompleteTaskActionDTO
{
    public function __construct(
        public Task $task,
        public int $rating,
        public ?string $comment = null,
    ) {
    }
}
