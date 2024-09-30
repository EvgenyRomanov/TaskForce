<?php

namespace App\Actions\Response;

use App\Actions\Auth\IsCustomerAction;
use App\Actions\Response\DTO\RejectResponseDTO;
use App\Exception\AppDomainException;
use App\Models\Status;
use App\Models\User;
use Illuminate\Auth\AuthManager;

class RejectResponseAction
{
    protected AuthManager $authManager;
    protected IsCustomerAction $isCustomerAction;

    public function __construct(AuthManager $authManager, IsCustomerAction $isCustomerAction)
    {
        $this->authManager = $authManager;
        $this->isCustomerAction = $isCustomerAction;
    }

    public function __invoke(RejectResponseDTO $rejectResponseDTO): void
    {
        ($this->isCustomerAction)($this->authManager);

        /** @var User $user */
        $user = $this->authManager->user();
        $task = $rejectResponseDTO->task;
        if ($task->customer_id != $user->id) abort(403);
        if ($task->status->name !== Status::NEW)
            throw new AppDomainException("You cannot reject a response to a task with the '{$task->status->name}' status.");

        $response = $rejectResponseDTO->response;
        $response->active = false;
        $response->save();
    }
}
