<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppraisalRequest extends FormRequest
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
            'appraisee_id' => ['required', Rule::exists('users', 'id'), 'unique:appraisals,appraisee_id'],
            'appraiser_id' => ['required', Rule::exists('users', 'id')],
            'approvers' => ['required', 'array'],
            'approvers.*.user_id' => ['required', Rule::exists('users', 'id')],
            'approvers.*.sequence' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'appraisee_id.exists' => 'The selected appraisee is invalid.',
            'appraisee_id.unique' => 'The selected appraisee already has an appraisal.',
            'appraiser_id.exists' => 'The selected appraiser is invalid.',
            'approvers.*.user_id.exists' => 'One of the selected approvers is invalid.',
            'approvers.*.sequence.min' => 'Each approver sequence must be at least 1.',
        ];
    }
}
