<?php

namespace Tests\Feature\Actions\Task;

use App\Actions\Task\CompleteTaskAction;
use App\Actions\Task\DTO\CompleteTaskActionDTO;
use App\Exception\AppDomainException;
use App\Models\Role;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CompleteTaskActionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @throws BindingResolutionException
     */
    public function test_action_positive()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        /** @var Task $task */
        $task = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::IN_PROGRESS)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);

        Auth::setUser($userCustomer);
        $action = app()->make(CompleteTaskAction::class);
        $action(new CompleteTaskActionDTO(
            $task,
            5,
        ));

        $this->assertEquals(Status::DONE, $task->status->name);
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_action_negative_1()
    {
        $this->expectException(HttpException::class);

        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        /** @var Task $task */
        $task = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::IN_PROGRESS)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);

        Auth::setUser($userExecutor);
        $action = app()->make(CompleteTaskAction::class);
        $action(new CompleteTaskActionDTO(
            $task,
            5,
        ));
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_action_negative_2()
    {
        $this->expectException(AppDomainException::class);

        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        /** @var Task $task */
        $task = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);

        Auth::setUser($userCustomer);
        $action = app()->make(CompleteTaskAction::class);
        $action(new CompleteTaskActionDTO(
            $task,
            5,
        ));
    }
}
