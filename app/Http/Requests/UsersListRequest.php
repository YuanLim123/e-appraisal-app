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
            'joinAfter' => ['date'],
            'joinBefore' => ['date'],
            'sortBy' => Rule::in(['last_name', 'first_name', 'join_at', 'email']),
            'sortOrder' => Rule::in(['asc', 'desc']),
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
