<?php

namespace App\Http\Requests\Task;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'title' => 'required',
            'description' => 'required',
            'files' => 'max:2',
            'category' => 'required|exists:categories,name',
            'budget' => 'integer|nullable',
            'deadline' => 'nullable|date|after:current|date_format:Y-m-d',
        ];
    }
}
