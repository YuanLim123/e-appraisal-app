<?php

namespace Tests;

use App\Enums\AppraisalRecordGrade;
use App\Enums\AppraisalRecordPurposeType;
use App\Enums\AppraisalRecordStatus;
use App\Models\Appraisal;
use App\Models\AppraisalRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createSubmittedAppraisalRecord(bool $isSupervisionAppraisal = false): AppraisalRecord
    {
        // create approvers
        $approver1 = User::factory()->create();
        $approver2 = User::factory()->create();

        $appraisee = User::factory()->create([
            'position_id' => $isSupervisionAppraisal ? 7 : 2,
        ]);

        $appraiser = User::factory()->create();

        $appraisalRecord = AppraisalRecord::factory()
            ->create([
                'current_step' => 1,
                'season_id' => 1,
                'current_approver_id' => $approver1->id,
                'grade' => AppraisalRecordGrade::SATISFACTORY->value,
                'grade_description' => AppraisalRecordGrade::SATISFACTORY->description(),
                'purpose' => AppraisalRecordPurposeType::CONFIRMATION_OR_PROMOTION->value,
                'status' => AppraisalRecordStatus::SUBMITTED->value,
                'employee_agreed_at' => now(),
                'supervisor_agreed_at' => now(),
                'total' => fake()->numberBetween(50, 100),
                'position_id' => $appraisee->position_id,
                'role_id' => $appraisee->role_id,
                'appraisee_id' => $appraisee->id,
                'appraiser_id' => $appraiser->id,
                'current_approver_id' => $approver1->id,
            ]);

        // assign approvers to the appraisal record
        $appraisalRecord->approvers()->createMany([
            ['sequence' => 1, 'user_id' => $approver1->id],
            ['sequence' => 2, 'user_id' => $approver2->id],
        ]);

        return $appraisalRecord;
    }

    protected function createUnsubmittedAppraisalRecord(?Appraisal $appraisal = null): AppraisalRecord
    {
        $appraisee = $appraisal?->appraisee ?? User::factory()->create();
        $appraiser = $appraisal->appraiser ?? User::factory()->create();

        $appraisalRecord = AppraisalRecord::factory()
            ->create([
                'season_id' => 1,
                'grade' => AppraisalRecordGrade::SATISFACTORY->value,
                'grade_description' => AppraisalRecordGrade::SATISFACTORY->description(),
                'purpose' => AppraisalRecordPurposeType::CONFIRMATION_OR_PROMOTION->value,
                'status' => AppraisalRecordStatus::CREATED->value,
                'employee_agreed_at' => null,
                'supervisor_agreed_at' => null,
                'total' => fake()->numberBetween(50, 100),
                'position_id' => $appraisee->position_id,
                'role_id' => $appraisee->role_id,
                'appraisee_id' => $appraisee->id,
                'appraiser_id' => $appraiser->id,
                'current_approver_id' => null,
            ]);

        return $appraisalRecord;
    }

    protected function createAppraisalRecordInputData(bool $isSupervisionAppraisal = false): array
    {
        $appraisalRecordData = AppraisalRecord::factory()->make()->toArray();
        if ($isSupervisionAppraisal) {
            $appraisalRecordData['performance'] = [
                [
                    'goal' => fake()->sentence(10),
                    'result' => fake()->sentence(15),
                    'rating' => fake()->numberBetween(10, 90),
                ],
            ];
            $appraisalRecordData['section_percentage'] = [
                fake()->numberBetween(10, 70),
                fake()->numberBetween(10, 70),
            ];
        }

        return $appraisalRecordData;
    }

    protected function createAppraisal(bool $isApraiseeHighPosition = false): Appraisal
    {
        $appraisee = User::factory()->create();
        $appraisee->position_id = $isApraiseeHighPosition ? 7 : 2;
        $appraisee->save();

        $appraiser = User::factory()->create();

        $approver1 = User::factory()->create();
        $approver2 = User::factory()->create();

        $appraisal = Appraisal::create([
            'appraisee_id' => $appraisee->id,
            'appraiser_id' => $appraiser->id,
        ]);

        $appraisal->approvers()->createMany([
            ['sequence' => 1, 'user_id' => $approver1->id],
            ['sequence' => 2, 'user_id' => $approver2->id],
        ]);

        return $appraisal;
    }
}
