<?php

namespace App\Policies;

use App\Models\Appraisal;
use App\Models\AppraisalRecord;
use App\Models\User;
use App\Exceptions\UserHasNoAppraisalCreatedException;
use Illuminate\Auth\Access\Response;

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
    public function create(User $user, ?Appraisal $appraisal): Response
    {
        if (empty($appraisal)) {
            // return Response::denyWithStatus(422, 'The user does not have an appraisal. Please create an appraisal first before creating an appraisal record.');
            throw new UserHasNoAppraisalCreatedException(); // handle in global exception handler
        }

        // only appraiser can create appraisal record for the appraisee
        if (auth()->id() === $appraisal->appraiser_id) {
            return Response::allow();
        }

        return Response::deny();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AppraisalRecord $appraisalRecord): bool
    {
        return $user->id === $appraisalRecord->appraiser_id;
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
}
