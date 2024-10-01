<?php

namespace Tests\Feature\Repository;

use App\Models\Category;
use App\Models\City;
use App\Models\Role;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Repository\DTO\MyTaskRepositoryDTO;
use App\Repository\DTO\NewTaskRepositoryDTO;
use App\Repository\TaskRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaskRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_my_tasks_executor()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        $tasksInProgress = Task::factory(2)->create([
            'status_id' => Status::query()->where('name', '=', Status::IN_PROGRESS)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        $tasksDone = Task::factory(2)->create([
            'status_id' => Status::query()->where('name', '=', Status::DONE)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        $taskNew = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);

        $taskRepo = new TaskRepository();
        $result = $taskRepo->getMyTasksByFilter(new MyTaskRepositoryDTO(
            $userExecutor,
            Status::IN_PROGRESS,
        ));

        $this->assertEquals(2, $result->count());
        foreach ($result as $task) {
            $this->assertEquals(Status::IN_PROGRESS, $task->status->name);
        }

        $result = $taskRepo->getMyTasksByFilter(new MyTaskRepositoryDTO(
            $userExecutor,
            Status::DONE,
        ));

        $this->assertEquals(2, $result->count());
        foreach ($result as $task) {
            $this->assertEquals(Status::DONE, $task->status->name);
        }

        $result = $taskRepo->getMyTasksByFilter(new MyTaskRepositoryDTO(
            $userExecutor,
            Status::NEW,
        ));

        $this->assertEquals(1, $result->count());
        foreach ($result as $task) {
            $this->assertEquals(Status::NEW, $task->status->name);
        }
    }

    public function test_my_tasks_customer()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        $tasksInProgress = Task::factory(2)->create([
            'status_id' => Status::query()->where('name', '=', Status::IN_PROGRESS)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        $tasksDone = Task::factory(2)->create([
            'status_id' => Status::query()->where('name', '=', Status::DONE)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        $taskNew = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);

        $taskRepo = new TaskRepository();
        $result = $taskRepo->getMyTasksByFilter(new MyTaskRepositoryDTO(
            $userCustomer,
            Status::IN_PROGRESS,
        ));

        $this->assertEquals(2, $result->count());
        foreach ($result as $task) {
            $this->assertEquals(Status::IN_PROGRESS, $task->status->name);
        }

        $result = $taskRepo->getMyTasksByFilter(new MyTaskRepositoryDTO(
            $userCustomer,
            Status::DONE,
        ));

        $this->assertEquals(2, $result->count());
        foreach ($result as $task) {
            $this->assertEquals(Status::DONE, $task->status->name);
        }

        $result = $taskRepo->getMyTasksByFilter(new MyTaskRepositoryDTO(
            $userCustomer,
            Status::NEW,
        ));

        $this->assertEquals(1, $result->count());
        foreach ($result as $task) {
            $this->assertEquals(Status::NEW, $task->status->name);
        }
    }

    public function test_my_tasks_executor_expired()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        $tasksInProgress = Task::factory(2)->create([
            'status_id' => Status::query()->where('name', '=', Status::IN_PROGRESS)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        $tasksDone = Task::factory(2)->create([
            'status_id' => Status::query()->where('name', '=', Status::DONE)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        $taskNew = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        $taskExpired1 = Task::factory(2)->create([
            'status_id' => Status::query()->where('name', '=', Status::IN_PROGRESS)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
            'deadline' => Carbon::now()->subDays(4),
        ]);
        $taskExpired2 = Task::factory(2)->create([
            'status_id' => Status::query()->where('name', '=', Status::DONE)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
            'deadline' => Carbon::now()->subDays(4),
        ]);

        $taskRepo = new TaskRepository();
        $result = $taskRepo->getMyTasksByFilter(new MyTaskRepositoryDTO(
            $userExecutor,
            'expired',
        ));

        $this->assertEquals(2, $result->count());
        foreach ($result as $task) {
            $this->assertEquals(Status::IN_PROGRESS, $task->status->name);
            $this->assertEquals(
                Carbon::now()->subDays(4)->format('Y-m-d'),
                Carbon::parse($task->deadline)->format('Y-m-d')
            );
        }
    }

    public function test_new_tasks()
    {
        $city = City::query()->first();
        $categoryFirst = Category::all()->first();
        $categoryLast = Category::all()->last();

        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        $tasksInProgress = Task::factory(2)->create([
            'status_id' => Status::query()->where('name', '=', Status::IN_PROGRESS)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
            'city_id' => $city->id,
        ]);
        $tasksDone = Task::factory(2)->create([
            'status_id' => Status::query()->where('name', '=', Status::DONE)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
            'city_id' => $city->id,
        ]);
        $taskNew = Task::factory(4)->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
            'category_id' => $categoryFirst->id,
            'city_id' => $city->id,
        ]);
        $taskNewCoordsIsNull = Task::factory(4)->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
            'category_id' => $categoryLast->id,
            'city_id' => null,
            'lat' => null,
            'long' => null,
        ]);

        $taskRepo = new TaskRepository();
        $result = $taskRepo->getNewTasksByFilter(new NewTaskRepositoryDTO(
            remoteWork: false,
            withoutResponse: false,
            categories: [],
            cityId: $city->id,
        ));
        $this->assertTrue($result->total() >= 8);

        $result = $taskRepo->getNewTasksByFilter(new NewTaskRepositoryDTO(
            remoteWork: true,
            withoutResponse: false,
            categories: [],
            cityId: $city->id,
        ));
        $this->assertTrue($result->total() >= 4);

        foreach ($result as $task) {
            $this->assertEquals(null, $task->city_id);
            $this->assertEquals(null, $task->lat);
            $this->assertEquals(null, $task->long);
        }

        $result = $taskRepo->getNewTasksByFilter(new NewTaskRepositoryDTO(
            remoteWork: false,
            withoutResponse: false,
            categories: [$categoryFirst->id, $categoryLast->id],
        ));
        $this->assertTrue($result->total() >= 8);

        foreach ($result as $task) {
            $this->assertTrue(in_array($task->category_id, [$categoryFirst->id, $categoryLast->id]));
        }
    }

    public function test_new_tasks_period()
    {
        $city = City::query()->first();

        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        $taskNew = Task::factory(4)->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
            'city_id' => $city->id,
        ]);
        $taskNew = Task::factory(4)->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
            'created_at' => Carbon::now()->subHour(10),
            'city_id' => $city->id,
        ]);

        $taskRepo = new TaskRepository();
        $result = $taskRepo->getNewTasksByFilter(new NewTaskRepositoryDTO(
            remoteWork: false,
            withoutResponse: false,
            categories: [],
            period: 1
        ));
        $this->assertTrue($result->total() >= 4);

        $result = $taskRepo->getNewTasksByFilter(new NewTaskRepositoryDTO(
            remoteWork: false,
            withoutResponse: false,
            categories: [],
            period: 12
        ));
        $this->assertTrue($result->total() >= 8);
    }
}
