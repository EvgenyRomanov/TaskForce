<?php

namespace Tests\Feature\Actions\Task;

use App\Actions\Task\CancelTaskAction;
use App\Models\Role;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CancelTaskActionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_action_positive()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var Task $task */
        $task = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $user->id,
        ]);

        Auth::setUser($user);
        $action = app()->make(CancelTaskAction::class);
        $action($task);

        $this->assertEquals(Status::CANCELED, $task->status->name);
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_action_negative_1()
    {
        $this->expectException(HttpException::class);

        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        /** @var User $user */
        $user = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);

        /** @var Task $task */
        $task = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $user->id
        ]);

        Auth::setUser($userExecutor);
        $action = app()->make(CancelTaskAction::class);
        $action($task);
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_action_negative_2()
    {
        $this->expectException(HttpException::class);

        /** @var User $user1 */
        $user1 = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var User $user2 */
        $user2 = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);

        /** @var Task $task */
        $task = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $user1->id
        ]);

        Auth::setUser($user2);
        $action = app()->make(CancelTaskAction::class);
        $action($task);
    }
}
