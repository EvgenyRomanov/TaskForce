<?php

namespace App\Actions\Response\DTO;

use App\Models\Response;
use App\Models\Task;

readonly class RejectResponseDTO
{
    public function __construct(
        public Task $task,
        public Response $response,
    ) {
    }
}
