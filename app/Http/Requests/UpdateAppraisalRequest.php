<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppraisalRequest extends FormRequest
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
            'appraiser_id' => ['required', Rule::exists('users', 'id')],
            'approvers' => ['required', 'array'],
            'approvers.*.user_id' => ['required', Rule::exists('users', 'id')],
            'approvers.*.sequence' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'appraiser_id.required' => 'Appraiser is required.',
            'approvers.required' => 'At least one approver is required.',
            'appraiser_id.exists' => 'The selected appraiser is invalid.',
            'approvers.*.user_id.exists' => 'One of the selected approvers is invalid.',
            'approvers.*.sequence.min' => 'Each approver sequence must be at least 1.',
        ];
    }
}
