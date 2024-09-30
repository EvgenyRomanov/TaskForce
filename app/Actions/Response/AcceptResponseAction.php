<?php

namespace App\Actions\Response;

use App\Actions\Auth\IsCustomerAction;
use App\Actions\Response\DTO\AcceptResponseDTO;
use App\Exception\AppDomainException;
use App\Models\Status;
use App\Models\User;
use Illuminate\Auth\AuthManager;

class AcceptResponseAction
{
    protected AuthManager $authManager;
    protected IsCustomerAction $isCustomerAction;

    public function __construct(AuthManager $authManager, IsCustomerAction $isCustomerAction)
    {
        $this->authManager = $authManager;
        $this->isCustomerAction = $isCustomerAction;
    }

    public function __invoke(AcceptResponseDTO $acceptResponseDTO): void
    {
        ($this->isCustomerAction)($this->authManager);

        /** @var User $user */
        $user = $this->authManager->user();
        $task = $acceptResponseDTO->task;
        if ($task->customer_id != $user->id) abort(403);
        if ($task->status->name !== Status::NEW)
            throw new AppDomainException("You cannot accept a response to a task with the '{$task->status->name}' status.");

        $task->status()->associate(Status::query()->where('name', '=', Status::IN_PROGRESS)->first());
        $task->executor()->associate(User::query()->find($acceptResponseDTO->executorId));
        $task->save();
    }
}
