<?php

namespace App\Services\V1;

use App\Enums\AppraisalRecordStatus;
use App\Events\AppraisalRecordSubmitted;
use App\Exceptions\AgreementRequiredException;
use App\Exceptions\AppraisalHasNoApproverException;
use App\Exceptions\UserHasNoAppraisalCreatedException;
use App\Models\AppraisalRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AppraisalRecordSubmitService
{
    public function submit(User $user, AppraisalRecord $appraisalRecord): void
    {
        if (! $appraisalRecord->employee_agreed_at ||! $appraisalRecord->supervisor_agreed_at) {
            throw new AgreementRequiredException;
        }

        $appraisal = $user->appraisalAsAppraisee;

        if (! $appraisal) {
            throw new UserHasNoAppraisalCreatedException;
        }

        $currentApprovers = $appraisal->approvers;

        if (! $currentApprovers) {
            throw new AppraisalHasNoApproverException;
        }

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
                'current_approver_id' => $currentApprovers->first()->user_id,
            ]);
        });

        AppraisalRecordSubmitted::dispatch($appraisalRecord);
    }
}
