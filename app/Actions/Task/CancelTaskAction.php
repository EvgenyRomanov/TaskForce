<?php

namespace App\Actions\Task;

use App\Actions\Auth\IsCustomerAction;
use App\Exception\AppDomainException;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\AuthManager;

class CancelTaskAction
{
    protected AuthManager $authManager;
    protected IsCustomerAction $isCustomerAction;

    public function __construct(AuthManager $authManager, IsCustomerAction $isCustomerAction)
    {
        $this->authManager = $authManager;
        $this->isCustomerAction = $isCustomerAction;
    }

    public function __invoke(Task $task): void
    {
        if (Status::NEW !== $task->status->name)
            throw new AppDomainException("You cannot cancel a task with the '{$task->status->name}' status.");

        ($this->isCustomerAction)($this->authManager);
        /** @var User $authUser */
        $authUser = $this->authManager->user();
        if ($task->customer->id !== $authUser->id) abort(403);

        $task->status()->associate(Status::query()->where('name', '=', Status::CANCELED)->first());
        $task->save();
    }
}
