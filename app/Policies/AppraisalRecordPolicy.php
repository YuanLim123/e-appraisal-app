<?php

namespace App\Policies;

use App\Enums\AppraisalRecordStatus;
use App\Exceptions\UserHasNoAppraisalCreatedException;
use App\Models\Appraisal;
use App\Models\AppraisalRecord;
use App\Models\User;

class AppraisalRecordPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(): ?bool
    {
        if (auth()->user()->departments()->whereIn('name', ['HR', 'PAYROLL'])->exists()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AppraisalRecord $appraisalRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ?Appraisal $appraisal): bool
    {
        if (! $appraisal) {
            throw new UserHasNoAppraisalCreatedException; // handle in global exception handler
        }

        // only appraiser can create appraisal record for the appraisee
        if (auth()->id() !== $appraisal->appraiser_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AppraisalRecord $appraisalRecord, User $appraisee): bool
    {
        if ($appraisee->id !== $appraisalRecord->appraisee_id) {
            return false;
        }

        if (auth()->id() !== $appraisalRecord->appraiser_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AppraisalRecord $appraisalRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AppraisalRecord $appraisalRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AppraisalRecord $appraisalRecord): bool
    {
        return false;
    }

    public function approveAppraisalRecord(User $user, AppraisalRecord $appraisalRecord): bool
    {
        return auth()->id() == $appraisalRecord->current_approver_id && $appraisalRecord->status == AppraisalRecordStatus::SUBMITTED;
    }

    public function addAttachment(User $user, AppraisalRecord $appraisalRecord): bool
    {
        if (auth()->id() !== $appraisalRecord->appraiser_id) {
            return false;
        }

        return true;
    }
}
