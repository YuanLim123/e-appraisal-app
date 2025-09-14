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
            'password' => ['required', 'string', Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'employee_no' => ['required', 'string', 'max:50', 'unique:users'],
            'join_at' => ['required', 'date'],
            'position_id' => ['nullable', Rule::exists('positions', 'id')],
            'role_id' => ['nullable', Rule::exists('roles', 'id')],
            'departments.*' => ['nullable', Rule::exists('departments', 'id')],
        ];
    }
}
