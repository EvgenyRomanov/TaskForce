<?php

namespace App\Actions\Task;

use App\Actions\Auth\IsExecutorAction;
use App\Exception\AppDomainException;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\AuthManager;

class RefuseTaskAction
{
    protected AuthManager $authManager;
    protected IsExecutorAction $isExecutorAction;

    public function __construct(AuthManager $authManager, IsExecutorAction $isExecutorAction)
    {
        $this->authManager = $authManager;
        $this->isExecutorAction = $isExecutorAction;
    }

    public function __invoke(Task $task): void
    {
        /** @var User $user */
        $user = $this->authManager->user();

        if (Status::IN_PROGRESS !== $task->status->name)
            throw new AppDomainException("You cannot reject an issue with the '{$task->status->name}' status.");

        ($this->isExecutorAction)($this->authManager);
        /** @var User $authUser */
        $authUser = $this->authManager->user();
        if ($task->executor->id !== $authUser->id) abort(403);

        $response = $user->responses()->where('task_id', $task->id)->first();
        $response->active = false;
        $response->save();
        $task->status()->associate(Status::query()->where('name', '=', Status::FAILED)->first());
        $task->save();
        $user->cnt_failed_tasks += 1;
        $user->save();
    }
}
