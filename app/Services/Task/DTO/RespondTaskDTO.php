<?php

namespace App\Services\Task\DTO;

use App\Models\Task;
use App\Models\User;

readonly class RespondTaskDTO
{
    public function __construct(
        public Task $task,
        public User $executor,
        public ?string $comment = null,
        public ?int $budget = null
    ) {
    }
}
