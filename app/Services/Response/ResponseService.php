<?php

namespace App\Services\Response;

use App\Models\Response;
use App\Services\Response\DTO\CreateResponseDTO;

class ResponseService
{
    public function create(CreateResponseDTO $responseDTO)
    {
        return Response::query()->create([
            'comment' => $responseDTO->comment,
            'budget' => $responseDTO->budget,
            'executor_id' => $responseDTO->executorId,
            'task_id' => $responseDTO->taskId,
        ]);
    }
}
