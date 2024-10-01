<?php

namespace App\Services\Task\DTO;

use App\Models\Task;
use App\Models\User;

readonly class RefuseTaskDTO
{
    public function __construct(
        public Task $task,
        public User $executor,
    ) {
    }
}
