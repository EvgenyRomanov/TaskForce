<?php

namespace App\Repository;

use App\Models\Status;
use App\Models\Task;
use App\Repository\DTO\MyTaskRepositoryDTO;
use App\Repository\DTO\NewTaskRepositoryDTO;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function getMyTasksByFilter(MyTaskRepositoryDTO $taskRepositoryDTO): Collection
    {
        $user = $taskRepositoryDTO->user;
        $status = $taskRepositoryDTO->status ?? ($user->isExecutor() ? Status::IN_PROGRESS : Status::NEW);

        if (in_array($status, [Status::IN_PROGRESS, Status::NEW])) {
            if ($user->isCustomer()) {
                return $user->customerTasks()->where(
                    'status_id',
                    '=',
                    Status::query()->where('name', '=', $status)->first()->id
                )->get();
            }

            return $user->executorTasks()->where(
                'status_id',
                '=',
                Status::query()->where('name', '=', $status)->first()->id
            )->get();
        }

        if ($status == Status::DONE) {
            if ($user->isCustomer()) {
                $statusIds = Status::query()->whereIn('name', [Status::DONE, Status::CANCELED, Status::FAILED])
                    ->get()->pluck('id')->toArray();
                return $user->customerTasks()->whereIn('status_id', $statusIds)->get();
            }

            $statusIds = Status::query()->whereIn('name', [Status::DONE, Status::FAILED])
                ->get()->pluck('id')->toArray();
            return $user->executorTasks()->whereIn('status_id', $statusIds)->get();
        }

        if ($user->isExecutor() && $status === 'expired') {
            return $user->executorTasks()->where(
                'status_id',
                '=',
                Status::query()->where('name', '=', Status::IN_PROGRESS)->first()->id
            )->where('deadline', '<', Carbon::now()->toDateTimeString())->get();
        }

        return new Collection([]);
    }

    public function getNewTasksByFilter(NewTaskRepositoryDTO $taskRepositoryDTO): LengthAwarePaginator
    {
        $status = Status::query()->where('name', '=', Status::NEW)->first();
        $query = Task::query()->where('status_id', '=', $status->id);

        if ($taskRepositoryDTO->remoteWork) {
            $query->where('city_id', '=', null);
        } elseif ($taskRepositoryDTO->cityId) {
            $query->where(function (Builder $query) use ($taskRepositoryDTO) {
                $query->where('city_id', '=', $taskRepositoryDTO->cityId)
                    ->orWhere('city_id', '=', null);
            });
        }

        if ($taskRepositoryDTO->withoutResponse) {
            $query->leftJoin('responses', 'tasks.id', '=', 'responses.task_id')
                ->where('responses.task_id', '=', null);
        }

        if ($taskRepositoryDTO->categories) {
            $query->whereIn('category_id', $taskRepositoryDTO->categories);
        }

        if ($taskRepositoryDTO->period) {
            $query->where('tasks.created_at', '>', Carbon::now()->subHour($taskRepositoryDTO->period)->toDateTimeString());
        } else {
            $query->where('tasks.created_at', '>', Carbon::now()->subHour(1)->toDateTimeString());
        }

        return $query->select('tasks.*')->paginate(3)->withQueryString();
    }
}
