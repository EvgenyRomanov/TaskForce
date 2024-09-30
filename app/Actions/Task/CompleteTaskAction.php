<?php

namespace App\Actions\Task;

use App\Actions\Auth\IsCustomerAction;
use App\Actions\Task\DTO\CompleteTaskActionDTO;
use App\Exception\AppDomainException;
use App\Models\Status;
use App\Models\User;
use App\Services\Feedback\DTO\CreateFeedbackDTO;
use App\Services\Feedback\FeedbackService;
use Illuminate\Auth\AuthManager;

class CompleteTaskAction
{
    protected AuthManager $authManager;
    protected IsCustomerAction $isCustomerAction;
    protected FeedbackService $feedbackService;

    public function __construct(
        AuthManager $authManager,
        IsCustomerAction $isCustomerAction,
        FeedbackService $feedbackService
    ) {
        $this->authManager = $authManager;
        $this->isCustomerAction = $isCustomerAction;
        $this->feedbackService = $feedbackService;
    }

    public function __invoke(CompleteTaskActionDTO $completeTaskActionDTO): void
    {
        /** @var User $user */
        $user = $this->authManager->user();
        $task = $completeTaskActionDTO->task;

        if (Status::IN_PROGRESS !== $task->status->name)
            throw new AppDomainException("You cannot complete a task with the '{$task->status->name}' status.");

        ($this->isCustomerAction)($this->authManager);
        /** @var User $authUser */
        $authUser = $this->authManager->user();
        if ($task->customer->id !== $authUser->id) abort(403);

        $task->status()->associate(Status::query()->where('name', '=', Status::DONE)->first());
        $task->save();
        $createFeedbackDTO = new CreateFeedbackDTO(
            customerId: $user->id,
            executorId: $task->executor->id,
            taskId: $task->id,
            rating: $completeTaskActionDTO->rating,
            comment: $completeTaskActionDTO->comment,
        );
        $this->feedbackService->create($createFeedbackDTO);
    }
}
