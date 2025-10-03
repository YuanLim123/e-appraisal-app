<?php

namespace App\Services\V1\User;

use App\Enums\AppraisalRecordStatus;
use App\Events\AppraisalRecordApproved;
use App\Events\AppraisalRecordCompleted;
use App\Events\ApprovalProceeded;
use App\Exceptions\ApproverNotFoundException;
use App\Models\AppraisalRecord;

class ApprovalService
{
    public function approve(AppraisalRecord $appraisalRecord, array $attributes): void
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

        $nextApprover = $appraisalRecord->getApprover($nextStep);

        // check if there is next approver
        // if yes, update the current_approver_id to next approver and step
        if ($nextApprover) {
            $appraisalRecord->update([
                'current_approver_id' => $nextApprover->user_id,
                'current_step' => $nextStep,
            ]);
            ApprovalProceeded::dispatch($appraisalRecord);
        } else {
            // if no, update the status to completed
            $appraisalRecord->update([
                'status' => AppraisalRecordStatus::COMPLETED,
                'completed_at' => now(),
            ]);
            AppraisalRecordCompleted::dispatch($appraisalRecord);
        }
    }

    public function reject(AppraisalRecord $appraisalRecord, array $attributes): void
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
            'rejected_at' => $attributes['date'] ?? now(),
        ]);

        $appraisalRecord->update([
            'status' => AppraisalRecordStatus::REJECTED,
        ]);
    }
}
