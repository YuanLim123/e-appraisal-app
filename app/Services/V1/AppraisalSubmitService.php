<?php

namespace App\Services\V1;

use App\Enums\AppraisalRecordGrade;
use App\Enums\AppraisalRecordPurposeType;
use App\Enums\AppraisalRecordStatus;
use App\Enums\AppraisalRecordType;
use App\Events\AppraisalRecordSubmitted;
use App\Exceptions\InvalidAppraisalSeasonException;
use App\Exceptions\InvalidRatingSumException;
use App\Exceptions\InvalidWeightAgeException;
use App\Exceptions\RecordAlreadyExistsInSeasonException;
use App\Models\AppraisalRecord;
use App\Models\Season;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AppraisalSubmitService
{

    public function submit(User $user, AppraisalRecord $appraisalRecord): AppraisalRecord
    {
        $appraisal = $user->appraisalAsAppraisee;

        if (empty($appraisal)) {
            throw new \Error; // create expection for this
        }

        $currentApprovers = $appraisal->approvers;

        if (empty($currentApprovers)) {
            throw new \Error;
        } // create expection for this

        DB::transaction(function () use ($appraisalRecord, $currentApprovers) {
            $appraisalRecord->approvers()->delete();

            // here we create value from current approver to record approver
            foreach ($currentApprovers as $approver) {
                $appraisalRecord->approvers()->create([
                    'sequence' => $approver->sequence,
                    'user_id' => $approver->user_id,
                ]);
            }

            $appraisalRecord->update([
                'status' => AppraisalRecordStatus::SUBMITTED->value,
                'current_step' => 1,
            ]);
        });

        AppraisalRecordSubmitted::dispatch($appraisalRecord);

        return $appraisalRecord;
    }
}
