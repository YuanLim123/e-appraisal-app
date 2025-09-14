<?php

namespace App\Services\V1\Admin;

use App\Models\Appraisal;
use App\Models\User;

class AppraisalService
{
    public function store(User $user, array $attributes): Appraisal
    {
        $appraisal = $user->appraisalAsAppraisee()
            ->create([
                'appraiser_id' => $attributes['appraiser_id'],
            ]);

        $appraisal->approvers()->createMany($attributes['approvers']);

        $appraisal->load(['appraiser', 'appraisee', 'approvers', 'approvers.user']);

        return $appraisal;
    }

    public function update(Appraisal $appraisal, array $attributes): Appraisal
    {
        if (! empty($attributes['appraiser_id'])) {
            $appraisal->update([
                'appraiser_id' => $attributes['appraiser_id'],
            ]);
        }

        if (! empty($attributes['approvers'])) {
            $appraisal->approvers()->delete();
            $appraisal->approvers()->createMany($attributes['approvers']);
        }

        $appraisal->load(['appraiser', 'appraisee', 'approvers', 'approvers.user']);

        return $appraisal;
    }
}
