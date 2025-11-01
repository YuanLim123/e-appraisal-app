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
        ];

        if ($user->isHigherRole()) {
            $rules['section_one'] = ['required', 'array'];
            $rules['section_one.*.goal'] = ['required'];
            $rules['section_one.*.result'] = ['required'];
            $rules['section_one.*.rating'] = ['required', 'numeric'];
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
            'section_one.*.goal.required' => 'All the fields in section I must be filled.',
            'section_one.*.result.required' => 'All the fields in section I must be filled.',
            'section_one.*.rating.required' => 'All the fields in section I must be filled.',
        ];
    }
}
