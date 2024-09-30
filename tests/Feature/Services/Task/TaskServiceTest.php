<?php

namespace Tests\Feature\Services\Task;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\Geo\GetCoordsInterface;
use App\Services\Task\DTO\CreateTaskDTO;
use App\Services\Task\TaskService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_action_1()
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

    public function test_action_2()
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
}
