<?php

namespace App\Services\V1\HR;

use App\Models\Appraisal;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AppraisalService
{
    public function store(array $attributes): Appraisal
    {
        /** @var Appraisal $appraisal */
        $appraisal = Appraisal::create([
            'appraiser_id' => $attributes['appraiser_id'],
            'appraisee_id' => $attributes['appraisee_id'],
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

        DB::transaction(function () use ($appraisal, $attributes) {
            if (! empty($attributes['approvers'])) {
                $appraisal->approvers()->delete();
                $appraisal->approvers()->createMany($attributes['approvers']);
            }
        });
        
        $appraisal->load(['appraiser', 'appraisee', 'approvers', 'approvers.user']);

        return $appraisal;
    }
}
