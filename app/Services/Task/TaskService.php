<?php

namespace App\Services\Task;

use App\Exception\AppDomainException;
use App\Models\Category;
use App\Models\City;
use App\Models\File;
use App\Models\Status;
use App\Models\Task;
use App\Services\Feedback\DTO\CreateFeedbackDTO;
use App\Services\Feedback\FeedbackService;
use App\Services\Geo\GetCoordsInterface;
use App\Services\Response\DTO\CreateResponseDTO;
use App\Services\Response\ResponseService;
use App\Services\Task\DTO\CompleteTaskDTO;
use App\Services\Task\DTO\CreateTaskDTO;
use App\Services\Task\DTO\RefuseTaskDTO;
use App\Services\Task\DTO\RespondTaskDTO;
use Illuminate\Http\UploadedFile;

class TaskService
{
    protected GetCoordsInterface $getCoordsService;
    protected FeedbackService $feedbackService;
    protected ResponseService $responseService;

    public function __construct(
        GetCoordsInterface $getCoordsService,
        FeedbackService $feedbackService,
        ResponseService $responseService
    ) {
        $this->getCoordsService = $getCoordsService;
        $this->feedbackService = $feedbackService;
        $this->responseService = $responseService;
    }

    public function create(CreateTaskDTO $createTaskDTO): Task
    {
        if ($createTaskDTO->location) {
            list($cityName, $lon, $lat) = $this->getCoordsService->getCoorsByAddress($createTaskDTO->location);
            $city = City::query()->where('name', '=', $cityName)->first();
            if (! $city) list($lon, $lat) = [null, null];
        }

        $task = Task::query()->create([
            'title' => $createTaskDTO->title,
            'description' => $createTaskDTO->description,
            'category_id' => Category::query()->where('name', '=', $createTaskDTO->categoryName)->first()->id,
            'deadline' => $createTaskDTO->deadline,
            'budget' => $createTaskDTO->budget,
            'lat' => $lat ?? null,
            'long' => $lon ?? null,
            'city_id' => isset($city) ? $city->id : null,
            'address' => $createTaskDTO->location,
            'customer_id' => $createTaskDTO->customer->id,
            'status_id' => Status::query()->where('name', '=', Status::NEW)->first()->id,
        ]);

        $files = [];

        /** @var UploadedFile $file */
        foreach ($createTaskDTO->files as $file) {
            $path = basename($file->storeAs("public/tasks/{$task->id}", $file->getClientOriginalName()));
            $files[] = (new File(['path' => $path, 'size' => $file->getSize()]));
        }

        $task->files()->saveMany($files);

        return $task;
    }

    public function cancel(Task $task): void
    {
        if (Status::NEW !== $task->status->name)
            throw new AppDomainException("You cannot cancel a task with the '{$task->status->name}' status.");

        $task->status()->associate(Status::query()->where('name', '=', Status::CANCELED)->first());
        $task->save();
    }

    public function complete(CompleteTaskDTO $completeTaskActionDTO): void
    {
        $user = $completeTaskActionDTO->customer;
        $task = $completeTaskActionDTO->task;

        if (Status::IN_PROGRESS !== $task->status->name)
            throw new AppDomainException("You cannot complete a task with the '{$task->status->name}' status.");

        $task->status()->associate(Status::query()->where('name', '=', Status::DONE)->first());
        $task->save();
        $createFeedbackDTO = new CreateFeedbackDTO(
            customerId: $user->id,
            executorId: $task->executor->id,
            taskId: $task->id,
            rating: $completeTaskActionDTO->rating,
            comment: $completeTaskActionDTO->comment,
        );
        $this->feedbackService->create($createFeedbackDTO);
    }

    public function refuse(RefuseTaskDTO $refuseTaskDTO): void
    {
        $user = $refuseTaskDTO->executor;
        $task = $refuseTaskDTO->task;

        if (Status::IN_PROGRESS !== $task->status->name)
            throw new AppDomainException("You cannot reject an issue with the '{$task->status->name}' status.");

        $response = $user->responses()->where('task_id', $task->id)->first();
        $response->active = false;
        $response->save();
        $task->status()->associate(Status::query()->where('name', '=', Status::FAILED)->first());
        $task->save();
        $user->cnt_failed_tasks += 1;
        $user->save();
    }

    public function respond(RespondTaskDTO $respondTaskActionDTO): void
    {
        $user = $respondTaskActionDTO->executor;
        $task = $respondTaskActionDTO->task;

        if (Status::NEW !== $task->status->name || $user->wasRespondToTask($task->id))
            throw new AppDomainException("You cannot respond to a task with the '{$task->status->name}' status or respond again.");

        $responseCreateDTO = new CreateResponseDTO(
            taskId: $task->id,
            executorId: $user->id,
            comment: $respondTaskActionDTO->comment,
            budget: $respondTaskActionDTO->budget,
        );
        $this->responseService->create($responseCreateDTO);
    }
}
