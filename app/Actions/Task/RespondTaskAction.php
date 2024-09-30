<?php

namespace App\Actions\Task;

use App\Actions\Auth\IsExecutorAction;
use App\Actions\Task\DTO\RespondTaskActionDTO;
use App\Exception\AppDomainException;
use App\Models\Status;
use App\Models\User;
use App\Services\Response\DTO\CreateResponseDTO;
use App\Services\Response\ResponseService;
use Illuminate\Auth\AuthManager;

class RespondTaskAction
{
    protected AuthManager $authManager;
    protected IsExecutorAction $isExecutorAction;
    protected ResponseService $responseService;

    public function __construct(
        AuthManager $authManager,
        IsExecutorAction $isExecutorAction,
        ResponseService $responseService,
    ) {
        $this->authManager = $authManager;
        $this->isExecutorAction = $isExecutorAction;
        $this->responseService = $responseService;
    }

    public function __invoke(RespondTaskActionDTO $respondTaskActionDTO): void
    {
        /** @var User $user */
        $user = $this->authManager->user();
        $task = $respondTaskActionDTO->task;

        if (Status::NEW !== $task->status->name || $user->wasRespondToTask($task->id))
            throw new AppDomainException("You cannot respond to a task with the '{$task->status->name}' status or respond again.");

        ($this->isExecutorAction)($this->authManager);
        $responseCreateDTO = new CreateResponseDTO(
            taskId: $task->id,
            executorId: $user->id,
            comment: $respondTaskActionDTO->comment,
            budget: $respondTaskActionDTO->budget,
        );

        $this->responseService->create($responseCreateDTO);
    }
}
