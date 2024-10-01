<?php

namespace Feature\Services\Response;

use App\Exception\AppDomainException;
use App\Models\Response;
use App\Models\Role;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Services\Response\DTO\AcceptResponseDTO;
use App\Services\Response\DTO\RejectResponseDTO;
use App\Services\Response\ResponseService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ResponseServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_accept_response_positive()
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
        /** @var ResponseService $service */
        $service = app()->get(ResponseService::class);
        $service->accept(new AcceptResponseDTO(
            $task,
            $userExecutor->id
        ));

        $this->assertEquals($userExecutor->id, $task->executor->id);
        $this->assertEquals(Status::IN_PROGRESS, $task->status->name);
    }

    public function test_accept_response_negative()
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
            'status_id' => Status::query()->where('name', '=', Status::IN_PROGRESS)->first()->id,
            'customer_id' => $userCustomer->id,
        ]);
        /** @var Response $response */
        $response = Response::factory()->create([
            'executor_id' => $userExecutor->id,
            'task_id' => $task->id,
        ]);

        Auth::setUser($userCustomer);
        /** @var ResponseService $service */
        $service = app()->get(ResponseService::class);
        $service->accept(new AcceptResponseDTO(
            $task,
            $userExecutor->id
        ));
    }

    public function test_reject_response_positive()
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
        /** @var ResponseService $service */
        $service = app()->get(ResponseService::class);
        $service->reject(new RejectResponseDTO(
            $task,
            $response
        ));

        $this->assertEquals(null, $task->executor);
        $this->assertEquals(Status::NEW, $task->status->name);
        $this->assertEquals(0, $response->active);
    }

    public function test_reject_response_negative()
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
        /** @var ResponseService $service */
        $service = app()->get(ResponseService::class);
        $service->reject(new RejectResponseDTO(
            $task,
            $response
        ));
    }
}
