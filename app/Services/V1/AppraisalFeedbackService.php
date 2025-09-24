<?php

namespace App\Services\V1;

use App\Exceptions\InvalidWeightAgeException;
use App\Models\AppraisalRecord;
use App\Models\User;

class AppraisalFeedbackService
{
    public function __construct(private AppraisalSubmitService $service) {}

    public function store(User $user, AppraisalRecord $appraisalRecord, array $attributes, bool $isSubmit): AppraisalRecord
    {
        if (! empty($attributes['goal_next'])) {
            if (! $this->validateWeightAgeTotal($attributes['goal_next'])) {
                throw new InvalidWeightAgeException;
            }
        }

        $appraisalRecord->update([
            'feedback' => $attributes,
        ]);

        if ($isSubmit) {
            $this->service->submit($user, $appraisalRecord);
        }

        $appraisalRecord->load(['appraisee', 'appraiser']);

        return $appraisalRecord;
    }

    private function validateWeightAgeTotal(array $attributes)
    {
        $weightAges = array_column($attributes, 'weightage');

        return array_sum($weightAges) === 100;
    }
}
