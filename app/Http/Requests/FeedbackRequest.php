<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
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
            'current_salary' => ['nullable', 'numeric'],
            'last_salary' => ['nullable', 'numeric'],
            'new_salary' => ['nullable', 'numeric'],
            'increment_quantum' => ['nullable', 'numeric'],
            'previous_increment' => ['nullable', 'date'],
            'effective_date' => ['nullable', 'date'],
            'confirm' => ['nullable', 'boolean'],
            'defer_until' => ['nullable', 'array'],
            'defer_until.isDefer' => ['nullable', 'boolean'],
            'defer_until.text' => ['nullable', 'string', 'required_if:defer_until.isDefer,true'],
            'no_potential' => ['nullable', 'boolean'],
            'not_ready_for_promotion' => ['nullable', 'boolean'],
            'poor_performance' => ['nullable', 'boolean'],
            'promote' => ['nullable', 'array'],
            'promote.isPromote' => ['nullable', 'boolean'],
            'promote.text' => ['nullable', 'string', 'required_if:promote.isPromote,true'],
            'salary_adjustment' => ['nullable', 'boolean'],
            'terminate' => ['nullable', 'array'],
            'terminate.isTerminate' => ['nullable', 'boolean'],
            'terminate.text' => ['nullable', 'string', 'required_if:terminate.isTerminate,true'],
        ];

        if ($user->isHigherRole()) {
            $rules['goals_next'] = ['array'];
            $rules['goals_next.*.objectives'] = ['nullable', 'string', 'required_with:goals_next.*.specificAction,goals_next.*.weightage'];
            $rules['goals_next.*.specificAction'] = ['nullable', 'string', 'required_with:goals_next.*.objectives,goals_next.*.weightage'];
            $rules['goals_next.*.weightage'] = ['nullable', 'numeric', 'required_with:goals_next.*.objectives,goals_next.*.specificAction'];
            $rules['goals_next.*.total'] = ['nullable', 'numeric'];
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
            'goals_next.*.objectives.required_with' => 'Objectives is required when Specific Action or Weightage is filled.',
            'goals_next.*.specificAction.required_with' => 'Specific Action is required when Objectives or Weightage is filled.',
            'goals_next.*.weightage.required_with' => 'Weightage is required when Objectives or Specific Action is filled.',
        ];
    }
}
