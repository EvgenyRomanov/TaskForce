<?php

namespace App\Services\Task\DTO;

use App\Models\User;

readonly class CreateTaskDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public string $categoryName,
        public User $customer,
        public array $files,
        public ?string $deadline = null,
        public ?int $budget = null,
        public ?string $location = null,
    ) {
    }
}
