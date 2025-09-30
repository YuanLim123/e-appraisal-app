<?php

namespace App\Services\V1\User;

use App\Enums\AppraisalRecordStatus;
use App\Events\ApprovalProceeded;
use App\Exceptions\ApproverNotFoundException;
use App\Models\AppraisalRecord;

class ApprovalService
{
    public function approve(AppraisalRecord $appraisalRecord, array $attributes)
    {
        // get the approver from record approver table
        $approver = $appraisalRecord->approvers()
            ->where('user_id', auth()->id())
            ->where('sequence', $appraisalRecord->current_step)
            ->whereNull('approved_at')
            ->whereNull('rejected_at')
            ->first();

        if (! $approver) {
            throw new ApproverNotFoundException;
        }

        // update the comment, date  for that approver
        $approver->update([
            'comment' => $attributes['comment'] ?? null,
            'approved_at' => $attributes['date'] ?? now(),
        ]);

        // update step
        $currentStep = $appraisalRecord->current_step;
        $nextStep = $currentStep + 1;

        // check if there is next approver
        $nextApprover = $appraisalRecord->approvers()
            ->where('sequence', $nextStep)
            ->whereNull('approved_at')
            ->whereNull('rejected_at')
            ->first();

        // if yes, update the current_approver_id to next approver and step
        if ($nextApprover) {
            $appraisalRecord->update([
                'current_approver_id' => $nextApprover->user_id,
                'current_step' => $nextStep,
            ]);
        } else {
            // if no, update the status to completed
            $appraisalRecord->update([
                'status' => AppraisalRecordStatus::COMPLETED,
                'completed_at' => now(),
            ]);
        }

        // send notification to next approver

        // if no, update the status to completed

        if ($nextApprover) {
            ApprovalProceeded::dispatch($appraisalRecord);
        } else {
            // send notification to hr
        }

    }
}
