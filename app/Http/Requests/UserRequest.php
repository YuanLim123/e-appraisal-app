<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'office_phone' => ['nullable', 'string', 'max:20'],
            'employee_no' => ['required', 'string', 'max:50', 'unique:users'],
            'join_at' => ['required', 'date'],
            'is_login_enabled' => ['required', 'boolean'],
            'position_id' => ['required', Rule::exists('positions', 'id')],
            'role_id' => ['required', Rule::exists('roles', 'id')],
            'departments' => ['required', 'array'],
            'departments.*' => [Rule::exists('departments', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_no.unique' => 'The employee number has already been taken.',
            'role_id.required' => 'The role field is required.',
            'position_id.required' => 'The position field is required.',
        ];
    }
}
