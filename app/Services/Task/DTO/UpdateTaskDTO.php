<?php

namespace App\Services\Task\DTO;

readonly class UpdateTaskDTO
{
    public function __construct(public string $status)
    {
    }
}
