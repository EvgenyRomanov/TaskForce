<?php

namespace Tests\Feature\Actions\Response;

use App\Actions\Response\AcceptResponseAction;
use App\Actions\Response\DTO\AcceptResponseDTO;
use App\Actions\Response\DTO\RejectResponseDTO;
use App\Actions\Response\RejectResponseAction;
use App\Exception\AppDomainException;
use App\Models\Response;
use App\Models\Role;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class RejectResponseActionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
        ]);
        /** @var Response $response */
        $response = Response::factory()->create([
            'executor_id' => $userExecutor->id,
            'task_id' => $task->id,
        ]);

        Auth::setUser($userCustomer);
        $action = app()->get(RejectResponseAction::class);
        $action(new RejectResponseDTO(
            $task,
            $response
        ));

        $this->assertEquals(null, $task->executor);
        $this->assertEquals(Status::NEW, $task->status->name);
        $this->assertEquals(0, $response->active);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
        ]);
        /** @var Response $response */
        $response = Response::factory()->create([
            'executor_id' => $userExecutor->id,
            'task_id' => $task->id,
        ]);

        Auth::setUser($userExecutor);
        $action = app()->get(RejectResponseAction::class);
        $action(new RejectResponseDTO(
            $task,
            $response
        ));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
        /** @var User $userExecutor2 */
        $userExecutor2 = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        /** @var Task $task */
        $task = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::DONE)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor2->id,
        ]);
        /** @var Response $response */
        $response = Response::factory()->create([
            'executor_id' => $userExecutor2->id,
            'task_id' => $task->id,
        ]);

        Auth::setUser($userCustomer);
        $action = app()->get(RejectResponseAction::class);
        $action(new RejectResponseDTO(
            $task,
            $response
        ));
    }
}
