<?php

namespace App\Services\V1\User;

use App\Enums\AppraisalRecordGrade;
use App\Enums\AppraisalRecordPurposeType;
use App\Enums\AppraisalRecordStatus;
use App\Enums\AppraisalRecordType;
use App\Exceptions\InvalidAppraisalSeasonException;
use App\Exceptions\InvalidRatingSumException;
use App\Exceptions\RecordAlreadyExistsInSeasonException;
use App\Models\AppraisalRecord;
use App\Models\Season;
use App\Models\User;

class AppraisalRecordService
{
    /**
     * @param  array{purpose: string, review_from: string, review_to: string, total?: float, performance?: array, section_percentage?: array}  $attributes
     */
    public function store(User $user, array $attributes): AppraisalRecord
    {
        $isHigherRole = $user->isHigherRole();

        $appraisal = $user->appraisalAsAppraisee;

        // check if the selected appraisal season exist
        $season = Season::query()
            ->where('purpose', $attributes['purpose'])
            ->whereNotNull('start_at')
            ->whereNull('end_at')
            ->orderBy('id', 'desc')
            ->first();

        if (
            $attributes['purpose'] != AppraisalRecordPurposeType::CONFIRMATION_OR_PROMOTION->value
            && ! $season
        ) {
            throw new InvalidAppraisalSeasonException;
        }

        // check if the user already has an appraisal record in the selected season
        $existingRecord = $user->appraisalRecordsAsAppraisee()
            ->where('season_id', $season->id)
            ->first();

        if ($existingRecord) {
            throw new RecordAlreadyExistsInSeasonException;
        }

        // validate that the sum of ratings in performance
        if ($isHigherRole && ! $this->validateRatingSum($attributes['performance'])) {
            throw new InvalidRatingSumException;
        }

        $weightedScore = 0;

        // calculate weighted total score
        if (! $isHigherRole) {
            $weightedScore = $attributes['total'] ?? 0;
        } else {
            $weightedScore = $this->calculateWeightedScore($attributes['section_percentage']);
        }

        $grade = $this->calculateGrade($weightedScore);

        $appraisalRecord = AppraisalRecord::create([
            'type' => $isHigherRole ? AppraisalRecordType::SUPERVISION->value : AppraisalRecordType::NORMAL->value,
            'purpose' => $attributes['purpose'],
            'grade' => $grade->value,
            'grade_description' => $grade->description(),
            'total' => $weightedScore,
            'status' => AppraisalRecordStatus::CREATED->value,
            'role_id' => $user->role_id,
            'position_id' => $user->position_id,
            'answer' => $attributes['performance'] ?? null,
            'review_from' => $attributes['review_from'],
            'review_to' => $attributes['review_to'],
            'appraiser_id' => $appraisal?->appraiser_id,
            'appraisee_id' => $user->id,
            'season_id' => $season ? $season->id : null,
        ]);

        $appraisalRecord->load(['appraisee', 'appraiser']);

        return $appraisalRecord;
    }

    /**
     * @param  array{purpose: string, review_from: string, review_to: string, total?: float, performance?: array, section_percentage?: array}  $attributes
     */
    public function update(User $user, AppraisalRecord $appraisalRecord, array $attributes): AppraisalRecord
    {
        $isHigherRole = $user->isHigherRole();

        $season = Season::query()
            ->where('purpose', $attributes['purpose'])
            ->whereNotNull('start_at')
            ->whereNull('end_at')
            ->orderBy('id', 'desc')
            ->first();

        if (
            $attributes['purpose'] != AppraisalRecordPurposeType::CONFIRMATION_OR_PROMOTION->value
            && ! $season
        ) {
            throw new InvalidAppraisalSeasonException;
        }

        // validate that the sum of ratings in performance
        if ($isHigherRole && ! $this->validateRatingSum($attributes['performance'])) {
            throw new InvalidRatingSumException;
        }

        $weighted_score = 0;

        // calculate weighted total score
        if (! $isHigherRole) {
            $weighted_score = $attributes['total'] ?? 0;
        } else {
            $weighted_score = $this->calculateWeightedScore($attributes['section_percentage']);
        }

        $grade = $this->calculateGrade($weighted_score);

        $appraisalRecord->update([
            'purpose' => $attributes['purpose'],
            'grade' => $grade->value,
            'grade_description' => $grade->label(),
            'total' => $weighted_score,
            'role_id' => $user->role_id,
            'position_id' => $user->position_id,
            'answer' => $attributes['performance'] ?? null,
            'review_from' => $attributes['review_from'],
            'review_to' => $attributes['review_to'],
            'season_id' => $season ? $season->id : null,
        ]);

        $appraisalRecord->load(['appraisee', 'appraiser']);

        return $appraisalRecord;
    }

    private function calculateWeightedScore(array $sectionPercentage): float
    {
        $total = 0;

        $sectionOneWeightage = 30;
        $sectionTwoWeightage = 70;
        $sectionWeighages = [$sectionOneWeightage, $sectionTwoWeightage];

        foreach ($sectionPercentage as $index => $value) {
            $sectionTotal = (float) ($value) * ($sectionWeighages[$index] / 100);
            $total += $sectionTotal;
        }

        return $total;
    }

    private function calculateGrade(float $total): AppraisalRecordGrade
    {
        switch ($total) {
            case $total >= 90:
                return AppraisalRecordGrade::EXCELLENT;
            case $total >= 80 && $total < 90:
                return AppraisalRecordGrade::GOOD;
            case $total >= 70 && $total < 80:
                return AppraisalRecordGrade::SATISFACTORY;
            case $total >= 60 && $total < 70:
                return AppraisalRecordGrade::BELOW_AVERAGE;
            default:
                return AppraisalRecordGrade::POOR;
        }
    }

    private function validateRatingSum(array $performance): bool
    {
        $total = 0;

        foreach ($performance as $item) {
            $total += (int) $item['rating'];
        }

        return $total <= 100;
    }
}
