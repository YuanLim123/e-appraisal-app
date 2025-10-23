<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsersListRequest extends FormRequest
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
            'sortBy' => Rule::in(['last_name', 'first_name', 'join_at', 'email']),
            'sortOrder' => Rule::in(['asc', 'desc']),
            'employee_no' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'department_id' => ['nullable', 'string', 'exists:departments,id'],
            'position_id' => ['nullable', 'string', 'exists:positions,id'],
            'role_id' => ['nullable', 'string', 'exists:roles,id'],
            'join_after' => ['nullable', 'date'],
            'join_before' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'sortBy' => 'The sortBy parameter must be one of the following: last_name, first_name, join_at, email.',
            'sortOrder' => 'The sortOrder parameter must be either asc or desc.',
        ];
    }
}
