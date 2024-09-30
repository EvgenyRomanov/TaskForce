<?php

namespace App\Http\Requests\Profile;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => 'nullable|digits:11',
            'birth_date' => 'nullable|date_format:Y-m-d',
            'telegram' => 'nullable|string|max:255',
            'categories' => 'exists:categories,id',
        ];
    }
}
