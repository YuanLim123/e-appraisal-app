<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user')->id)],
            'phone' => ['required', 'string', 'max:20'],
            'office_phone' => ['nullable', 'string', 'max:20'],
            'employee_no' => ['required', 'string', 'max:50', Rule::unique('users', 'employee_no')->ignore($this->route('user')->id)],
            'join_at' => ['required', 'date'],
            'position_id' => ['required', Rule::exists('positions', 'id')],
            'role_id' => ['required', Rule::exists('roles', 'id')],
            'department_ids' => ['required', 'array'],
            'department_ids.*' => [Rule::exists('departments', 'id')],
        ];
    }
}
