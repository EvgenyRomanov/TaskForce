<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\CancelTaskRequest;
use App\Http\Requests\Task\CompleteTaskRequest;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\MyTasksRequest;
use App\Http\Requests\Task\RefuseTaskRequest;
use App\Http\Requests\Task\RespondTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use App\Repository\DTO\MyTaskRepositoryDTO;
use App\Repository\DTO\NewTaskRepositoryDTO;
use App\Repository\TaskRepository;
use App\Services\Task\DTO\CompleteTaskDTO;
use App\Services\Task\DTO\CreateTaskDTO;
use App\Services\Task\DTO\RefuseTaskDTO;
use App\Services\Task\DTO\RespondTaskDTO;
use App\Services\Task\TaskService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, TaskRepository $taskRepository): View
    {
        $user = $request->user();
        $categoryIds = [];
        $data = $request->all();

        foreach ($data as $key => $value) {
            if (Str::contains($key, 'category_')) $categoryIds[] = (int)$value;
        }

        $newTaskRepositoryDTO = new NewTaskRepositoryDTO(
            remoteWork: (bool)$request->get('remote_work'),
            withoutResponse: (bool)$request->get('without_response'),
            categories: $categoryIds,
            period: (int)$request->get('period'),
            cityId: $user->city_id
        );
        $tasks = $taskRepository->getNewTasksByFilter($newTaskRepositoryDTO);
        $categories = Category::all();

        return view('components.task.new-tasks', compact('tasks', 'user', 'categories'));
    }

    public function myTasks(MyTasksRequest $request, TaskRepository $taskRepository): View
    {
        $user = $request->user();
        $taskRepositoryDTO = new MyTaskRepositoryDTO(
            user: $user,
            status: $request->get('status')
        );
        $tasks = $taskRepository->getMyTasksByFilter($taskRepositoryDTO);

        return view('components.task.my-tasks', compact('tasks', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateTaskRequest $request): View
    {
        $user = $request->user();
        $categories = Category::all();

        return view('components.task.create', compact('user', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreTaskRequest $request,
        TaskService $taskService,
    ): RedirectResponse {
        $data = $request->all();
        /** @var User $user */
        $user = $request->user();

        $createTaskDTO = new CreateTaskDTO(
            title: $data['title'],
            description: $data['description'],
            categoryName: $data['category'],
            customer: $user,
            files: $data['files'] ?? [],
            deadline: $data['deadline'] ?? null,
            budget: $data['budget'] ?? null,
            location: $data['location'] ?? null,
        );
        $task = $taskService->create($createTaskDTO);

        return Redirect::route('tasks.show', $task->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Task $task): View
    {
        $user = $request->user();
        return view('components.task.show', compact('task', 'user'));
    }

    public function cancel(CancelTaskRequest $request, Task $task, TaskService $taskService): RedirectResponse
    {
        $taskService->cancel($task);
        return Redirect::route('tasks.show', $task->id);
    }

    public function respond(RespondTaskRequest $request, Task $task, TaskService $taskService): RedirectResponse
    {
        $respondTaskActionDTO = new RespondTaskDTO(
            task: $task,
            executor: $request->user(),
            comment: $request->get('comment'),
            budget: $request->get('budget'),
        );
        $taskService->respond($respondTaskActionDTO);

        return Redirect::route('tasks.show', $task->id);
    }

    public function refuse(RefuseTaskRequest $request, Task $task, TaskService $taskService): RedirectResponse
    {
        $refuseTaskDTO = new RefuseTaskDTO(
            task: $task,
            executor: $request->user(),
        );
        $taskService->refuse($refuseTaskDTO);

        return Redirect::route('tasks.show', $task->id);
    }

    public function complete(CompleteTaskRequest $request, Task $task, TaskService $taskService): RedirectResponse
    {
        $completeTaskActionDTO = new CompleteTaskDTO(
            task: $task,
            customer: $request->user(),
            rating: $request->get('rating'),
            comment: $request->get('comment'),
        );
        $taskService->complete($completeTaskActionDTO);

        return Redirect::route('tasks.show', $task->id);
    }
}
