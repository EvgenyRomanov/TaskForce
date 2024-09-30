<?php

namespace App\Http\Controllers;

use App\Actions\Response\AcceptResponseAction;
use App\Actions\Response\DTO\AcceptResponseDTO;
use App\Actions\Response\DTO\RejectResponseDTO;
use App\Actions\Response\RejectResponseAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Response;
use App\Models\Task;
use Illuminate\Support\Facades\Redirect;

class ResponseController extends Controller
{
    public function accept(Request $request, Response $response, AcceptResponseAction $acceptResponseAction): RedirectResponse
    {
        $task = Task::query()->find($response->task_id);
        $acceptResponseDTO = new AcceptResponseDTO(
            task: $task,
            executorId: $response->executor_id,
        );
        $acceptResponseAction($acceptResponseDTO);

        return Redirect::route('tasks.show', $task->id);
    }

    public function reject(Request $request, Response $response, RejectResponseAction $rejectResponseAction): RedirectResponse
    {
        $task = Task::query()->find($response->task_id);
        $rejectResponseDTO = new RejectResponseDTO(
            task: $task,
            response: $response,
        );
        $rejectResponseAction($rejectResponseDTO);

        return Redirect::route('tasks.show', $task->id);
    }
}
