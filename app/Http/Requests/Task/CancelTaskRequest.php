<?php

namespace App\Http\Requests\Task;

use App\Models\Role;
use App\Services\Task\Interface\AllowedActionsOnTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CancelTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role->name === Role::CUSTOMER;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in([AllowedActionsOnTask::CANCEL])],
        ];
    }
}
