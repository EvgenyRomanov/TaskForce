<?php

namespace Tests\Feature\Services\Task;

use App\Exception\AppDomainException;
use App\Models\Response;
use App\Models\Role;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Services\Geo\GetCoordsInterface;
use App\Services\Task\DTO\CompleteTaskDTO;
use App\Services\Task\DTO\CreateTaskDTO;
use App\Services\Task\DTO\RefuseTaskDTO;
use App\Services\Task\DTO\RespondTaskDTO;
use App\Services\Task\TaskService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_create_task_with_location()
    {
        $city = 'Владимир';
        $long = 40.6;
        $lat = 56.17;
        $location = "{$city} Ленина";

        /** @var User $user */
        $user = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);

        $this->instance(
            GetCoordsInterface::class,
            Mockery::mock(GetCoordsInterface::class, function (MockInterface $mock) use ($city, $location, $long, $lat) {
                $mock->shouldReceive('getCoorsByAddress')->once()->with($location)->andReturn([
                    $city,
                    $long,
                    $lat,
                ]);
            })
        );

        /** @var TaskService $service */
        $service = app(TaskService::class);
        $task = $service->create(new CreateTaskDTO(
            'test title123',
            'test description123',
            'Фото',
                $user,
                [],
            location: $location
        ));

        $foundTask = Task::query()->find($task->id);
        $this->assertTrue(! is_null($foundTask));
        $this->assertEquals($city, $foundTask->city->name);
        $this->assertEquals($long, $foundTask->long);
    }

    public function test_create_task_without_location()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);

        /** @var TaskService $service */
        $service = app(TaskService::class);
        $task = $service->create(new CreateTaskDTO(
            'test title123',
            'test description123',
            'Фото',
            $user,
            [],
        ));

        $foundTask = Task::query()->find($task->id);
        $this->assertTrue(! is_null($foundTask));
        $this->assertEquals(null, $foundTask->city_id);
        $this->assertEquals(null, $foundTask->long);
    }

    public function test_cancel_task()
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
        /** @var TaskService $service */
        $service = app(TaskService::class);
        $service->cancel($task);

        $this->assertEquals(Status::CANCELED, $task->status->name);
    }

    public function test_complete_task_positive()
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
        /** @var TaskService $service */
        $service = app(TaskService::class);
        $service->complete(new CompleteTaskDTO(
            $task,
            $userCustomer,
            5,
        ));

        $this->assertEquals(Status::DONE, $task->status->name);
    }

    public function test_complete_task_negative()
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
        /** @var TaskService $service */
        $service = app(TaskService::class);
        $service->complete(new CompleteTaskDTO(
            $task,
            $userCustomer,
            5,
        ));
    }

    public function test_refuse_task_positive()
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
        /** @var TaskService $service */
        $service = app(TaskService::class);
        $service->refuse(new RefuseTaskDTO($task, $userExecutor));

        $this->assertEquals(Status::FAILED, $task->status->name);
        $this->assertEquals(1, $userExecutor->cnt_failed_tasks);
        $response->refresh();
        $this->assertEquals(0, $response->active);
    }

    public function test_refuse_task_negative()
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
        /** @var TaskService $service */
        $service = app(TaskService::class);
        $service->refuse(new RefuseTaskDTO($task, $userExecutor));
    }

    public function test_respond_task_positive()
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

        Auth::setUser($userExecutor);
        /** @var TaskService $service */
        $service = app(TaskService::class);
        $service->respond(new RespondTaskDTO(
            $task,
            $userExecutor
        ));

        $this->assertEquals(Status::NEW, $task->status->name);
        $this->assertTrue($userExecutor->wasRespondToTask($task->id));
    }

    public function test_respond_task_negative()
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
        ]);
        /** @var Response $response */
        $response = Response::factory()->create([
            'executor_id' => $userExecutor->id,
            'task_id' => $task->id,
        ]);

        Auth::setUser($userExecutor);
        /** @var TaskService $service */
        $service = app(TaskService::class);
        $service->respond(new RespondTaskDTO(
            $task,
            $userExecutor
        ));
    }
}
