<?php

namespace App\Http\Requests\Task;

use App\Services\Task\Interface\AllowedActionsOnTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in([AllowedActionsOnTask::COMPLETE])],
            'rating' => ['required', Rule::in([1, 2, 3, 4, 5])],
        ];
    }
}
