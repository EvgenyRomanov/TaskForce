<?php

namespace App\Http\Requests\Response;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class AcceptResponseRequest extends FormRequest
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
        return [];
    }
}