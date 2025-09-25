<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppraisalRecordFeedbackRequest extends FormRequest
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
        $isSubmit = $this->query('isSubmit', false);

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
            $rules['goal_next'] = ['array'];
            $rules['goal_next.*.objective'] = ['nullable', 'string', 'required_with:goal_next.*.specificAction,goal_next.*.weightage,goal_next.*.total'];
            $rules['goal_next.*.specificAction'] = ['nullable', 'string', 'required_with:goal_next.*.objective,goal_next.*.weightage,goal_next.*.total'];
            $rules['goal_next.*.weightage'] = ['nullable', 'numeric', 'required_with:goal_next.*.objective,goal_next.*.specificAction,goal_next.*.total'];
            $rules['goal_next.*.total'] = ['nullable', 'numeric'];
        }

        if ($isSubmit) {
            $rules['isEmployeeAgreed'] = ['required', 'boolean', 'accepted'];
            $rules['isSupervisorAgreed'] = ['required', 'boolean', 'accepted'];
        } else {
            $rules['isEmployeeAgreed'] = ['nullable', 'boolean'];
            $rules['isSupervisorAgreed'] = ['nullable', 'boolean'];
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
            'goal_next.*.objective.required_with' => 'Objectives is required when Specific Action, Weightage or Total is filled.',
            'goal_next.*.specificAction.required_with' => 'Specific Action is required when Objectives, Weightage Total is filled.',
            'goal_next.*.weightage.required_with' => 'Weightage is required when Objectives, Specific Action or Total is filled.',
        ];
    }
}
