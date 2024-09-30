<?php

namespace Tests\Feature\Actions\Task;

use App\Actions\Task\RefuseTaskAction;
use App\Exception\AppDomainException;
use App\Models\Response;
use App\Models\Role;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class RefuseTaskActionTest extends TestCase
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
        /** @var Response $response */
        $response = Response::factory()->create([
            'executor_id' => $userExecutor->id,
            'task_id' => $task->id,
        ]);

        Auth::setUser($userExecutor);
        $action = app()->make(RefuseTaskAction::class);
        $action($task);

        $this->assertEquals(Status::FAILED, $task->status->name);
        $this->assertEquals(1, $userExecutor->cnt_failed_tasks);
        $response->refresh();
        $this->assertEquals(0, $response->active);
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
        /** @var Response $response */
        $response = Response::factory()->create([
            'executor_id' => $userExecutor->id,
            'task_id' => $task->id,
        ]);

        Auth::setUser($userCustomer);
        $action = app()->make(RefuseTaskAction::class);
        $action($task);
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
            'status_id' => Status::query()->where('name', '=', Status::DONE)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        /** @var Response $response */
        $response = Response::factory()->create([
            'executor_id' => $userExecutor->id,
            'task_id' => $task->id,
        ]);

        Auth::setUser($userExecutor);
        $action = app()->make(RefuseTaskAction::class);
        $action($task);
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_action_negative_3()
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
        /** @var User $userExecutor2 */
        $userExecutor2 = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        /** @var Task $task */
        $task = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::IN_PROGRESS)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        /** @var Response $response */
        $response = Response::factory()->create([
            'executor_id' => $userExecutor->id,
            'task_id' => $task->id,
        ]);

        Auth::setUser($userExecutor2);
        $action = app()->make(RefuseTaskAction::class);
        $action($task);
    }
}
