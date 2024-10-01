<?php

namespace App\Http\Controllers;

use App\Http\Requests\Response\AcceptResponseRequest;
use App\Http\Requests\Response\RejectResponseRequest;
use App\Models\Response;
use App\Models\Task;
use App\Services\Response\DTO\AcceptResponseDTO;
use App\Services\Response\DTO\RejectResponseDTO;
use App\Services\Response\ResponseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class ResponseController extends Controller
{
    public function accept(AcceptResponseRequest $request, Response $response, ResponseService $responseService): RedirectResponse
    {
        $task = Task::query()->find($response->task_id);
        $acceptResponseDTO = new AcceptResponseDTO(
            task: $task,
            executorId: $response->executor_id,
        );
        $responseService->accept($acceptResponseDTO);

        return Redirect::route('tasks.show', $task->id);
    }

    public function reject(RejectResponseRequest $request, Response $response, ResponseService $responseService): RedirectResponse
    {
        $task = Task::query()->find($response->task_id);
        $rejectResponseDTO = new RejectResponseDTO(
            task: $task,
            response: $response,
        );
        $responseService->reject($rejectResponseDTO);

        return Redirect::route('tasks.show', $task->id);
    }
}
