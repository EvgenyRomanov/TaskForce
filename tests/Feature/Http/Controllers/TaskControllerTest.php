<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use App\Models\Response;
use App\Models\Role;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Services\Task\Interface\AllowedActionsOnTask;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_positive()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        Auth::setUser($userCustomer);
        $response = $this->get(route('tasks.index'));
        $response->assertStatus(200);
        $response->assertViewIs('components.task.new-tasks');
    }

    public function test_index_negative()
    {
        $response = $this->get(route('tasks.index'));
        $response->assertStatus(302);
        $response->assertRedirectToRoute('login');
    }

    public function test_my()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        Auth::setUser($userCustomer);
        $response = $this->get(route('tasks.my_tasks'));
        $response->assertStatus(200);
        $response->assertViewIs('components.task.my-tasks');
    }

    public function test_create()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        Auth::setUser($userCustomer);
        $response = $this->get(route('tasks.create'));
        $response->assertStatus(200);
        $response->assertViewIs('components.task.create');
    }

    public function test_store()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        Auth::setUser($userCustomer);
        $response = $this->post(route('tasks.store'), [
            'title' => 'Test task',
            'description' => 'Test task description',
            'category' => Category::query()->first()->name,
        ]);
        $response->assertStatus(302);
    }

    public function test_show()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        Auth::setUser($userCustomer);
        /** @var Task $taskNew */
        $taskNew = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
        ]);

        $response = $this->get(route('tasks.show', $taskNew->id));
        $response->assertViewIs('components.task.show');
    }

    public function test_cancel()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        Auth::setUser($userCustomer);
        /** @var Task $taskNew */
        $taskNew = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
            'customer_id' => $userCustomer->id,
        ]);

        $response = $this->put(route('tasks.cancel', $taskNew->id), [
            'action' => AllowedActionsOnTask::CANCEL
        ]);
        $response->assertStatus(302);
        $response->assertRedirectToRoute('tasks.show', $taskNew->id);
    }

    public function test_respond()
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
        ]);

        $response = $this->put(route('tasks.respond', $taskNew->id), [
            'action' => AllowedActionsOnTask::RESPOND
        ]);
        $response->assertStatus(302);
        $response->assertRedirectToRoute('tasks.show', $taskNew->id);
    }

    public function test_refuse()
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
            'status_id' => Status::query()->where('name', '=', Status::IN_PROGRESS)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        Response::factory()->create([
            'executor_id' => $userExecutor->id,
            'task_id' => $taskNew->id,
        ]);

        $response = $this->put(route('tasks.refuse', $taskNew->id), [
            'action' => AllowedActionsOnTask::REFUSE
        ]);
        $response->assertStatus(302);
        $response->assertRedirectToRoute('tasks.show', $taskNew->id);
    }

    public function test_complete()
    {
        /** @var User $userCustomer */
        $userCustomer = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        Auth::setUser($userCustomer);
        /** @var Task $taskNew */
        $taskNew = Task::factory()->create([
            'status_id' => Status::query()->where('name', '=', Status::IN_PROGRESS)->first()->id,
            'customer_id' => $userCustomer->id,
            'executor_id' => $userExecutor->id,
        ]);
        Response::factory()->create([
            'executor_id' => $userExecutor->id,
            'task_id' => $taskNew->id,
        ]);

        $response = $this->put(route('tasks.complete', $taskNew->id), [
            'action' => AllowedActionsOnTask::COMPLETE,
            'rating' => 5
        ]);
        $response->assertStatus(302);
        $response->assertRedirectToRoute('tasks.show', $taskNew->id);
    }
}
