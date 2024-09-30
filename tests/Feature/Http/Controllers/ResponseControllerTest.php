<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Response;
use App\Models\Role;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ResponseControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_accept()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        Auth::setUser($userExecutor);
        /** @var Task $taskNew */
        $taskNew = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        /** @var Response $responseToTask */
        $responseToTask = Response::factory()->create([
            'executor_id' => $userExecutor->id,
            'task_id' => $taskNew->id,
        ]);
        Auth::setUser($userCustomer);

        $response = $this->get(route('responses.accept', $responseToTask->id));
        $response->assertStatus(302);
        $response->assertRedirectToRoute('tasks.show', $taskNew->id);
    }

    public function test_reject()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        Auth::setUser($userExecutor);
        /** @var Task $taskNew */
        $taskNew = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        /** @var Response $responseToTask */
        $responseToTask = Response::factory()->create([
            'executor_id' => $userExecutor->id,
            'task_id' => $taskNew->id,
        ]);
        Auth::setUser($userCustomer);

        $response = $this->get(route('responses.reject', $responseToTask->id));
        $response->assertStatus(302);
        $response->assertRedirectToRoute('tasks.show', $taskNew->id);
    }
}
