<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Enums\AppraisalRecordPurposeType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $userId = $this->input('appraisee_id');
        $user = User::findOrFail($userId);

        $rules = [
            'review_from' => ['required', 'date', Rule::date()->format('Y-m-d')],
            'review_to' => ['required', 'date', Rule::date()->format('Y-m-d')],
            'purpose' => ['required', Rule::enum(AppraisalRecordPurposeType::class)],
            'section_one_answers' => ['required', 'array'],
            'section_one_answers.*.goal' => ['required'],
            'section_one_answers.*.result' => ['required'],
            'section_one_answers.*.rating' => ['required', 'numeric'],
        ];

        if ($user->isHigherRole()) {
            $rules['section_two_answers'] = ['nullable', 'array'];
            $rules['section_two_answers.*.comment'] = ['nullable'];
            $rules['section_two_answers.*.rating'] = ['nullable', 'numeric'];
            $rules['section_three_answers'] = ['nullable', 'array'];
            $rules['section_three_answers.*.comment'] = ['nullable'];
            $rules['section_three_answers.*.rating'] = ['nullable', 'numeric'];
            $rules['section_percentage'] = ['nullable', 'array'];
            $rules['is_section_three_enabled'] = ['nullable', 'boolean'];
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
            'section_one_answers.*.goal.required' => 'All the fields in section I must be filled.',
            'section_one_answers.*.result.required' => 'All the fields in section I must be filled.',
            'section_one_answers.*.rating.required' => 'All the fields in section I must be filled.',
        ];
    }
}
