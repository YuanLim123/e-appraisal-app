<?php

namespace App\Http\Requests;

use App\Enums\AppraisalRecordPurposeType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppraisalRecordRequest extends FormRequest
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
        $user = $this->route('user');

        $rules = [
            'review_from' => ['required', 'date', Rule::date()->format('Y-m-d')],
            'review_to' => ['required', 'date', Rule::date()->format('Y-m-d')],
            'purpose' => ['required', Rule::enum(AppraisalRecordPurposeType::class)],
        ];

        if ($user->isHigherRole()) {
            $rules['performance'] = ['required', 'array'];
            $rules['performance.*.goal'] = ['required'];
            $rules['performance.*.result'] = ['required'];
            $rules['performance.*.rating'] = ['required', 'numeric'];
            $rules['section_percentage'] = ['nullable', 'array'];
        } else {
            $rules['total'] = ['required', 'numeric'];
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'performance.*.goal.required' => 'All the fields in section 1 must be filled.',
            'performance.*.result.required' => 'All the fields in section 1 must be filled.',
            'performance.*.rating.required' => 'All the fields in section 1 must be filled.',
        ];
    }
}
