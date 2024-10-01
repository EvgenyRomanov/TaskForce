<?php

namespace App\Services\Task\DTO;

use App\Models\Task;
use App\Models\User;

readonly class CompleteTaskDTO
{
    public function __construct(
        public Task $task,
        public User $customer,
        public int $rating,
        public ?string $comment = null,
    ) {
    }
}
