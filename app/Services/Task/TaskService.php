<?php

namespace App\Services\Task;

use App\Models\Category;
use App\Models\City;
use App\Models\File;
use App\Models\Status;
use App\Models\Task;
use App\Services\Geo\GetCoordsInterface;
use App\Services\Task\DTO\CreateTaskDTO;
use Illuminate\Http\UploadedFile;

class TaskService
{
    protected GetCoordsInterface $getCoordsService;

    public function __construct(GetCoordsInterface $getCoordsService)
    {
        $this->getCoordsService = $getCoordsService;
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
}
