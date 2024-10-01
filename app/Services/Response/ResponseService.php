<?php

namespace App\Services\Response;

use App\Exception\AppDomainException;
use App\Models\Response;
use App\Models\Status;
use App\Models\User;
use App\Services\Response\DTO\AcceptResponseDTO;
use App\Services\Response\DTO\CreateResponseDTO;
use App\Services\Response\DTO\RejectResponseDTO;

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

    public function accept(AcceptResponseDTO $acceptResponseDTO): void
    {
        $task = $acceptResponseDTO->task;

        if ($task->status->name !== Status::NEW)
            throw new AppDomainException("You cannot accept a response to a task with the '{$task->status->name}' status.");

        $task->status()->associate(Status::query()->where('name', '=', Status::IN_PROGRESS)->first());
        $task->executor()->associate(User::query()->find($acceptResponseDTO->executorId));
        $task->save();
    }

    public function reject(RejectResponseDTO $rejectResponseDTO): void
    {
        $task = $rejectResponseDTO->task;

        if ($task->status->name !== Status::NEW)
            throw new AppDomainException("You cannot reject a response to a task with the '{$task->status->name}' status.");

        $response = $rejectResponseDTO->response;
        $response->active = false;
        $response->save();
    }
}
