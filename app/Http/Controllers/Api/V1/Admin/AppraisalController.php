<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Requests\StoreAppraisalRequest;
use App\Models\Appraisal;
use App\Http\Resources\AppraisalResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppraisalController extends Controller
{
    public function store(StoreAppraisalRequest $request)
    {
        // validate input
        $attributes = $request->validated();

        // create appraisal
        $appraisal = Appraisal::create([
            'appraisee_id' => $attributes['appraisee_id'],
            'appraiser_id' => $attributes['appraiser_id'],
        ]);

        // create approver roles
        $appraisal->approvers()->createMany($attributes['approvers']);

        // return resource
        $appraisal->load(['appraiser', 'appraisee', 'approvers', 'approvers.user']);

        return new AppraisalResource($appraisal);
    }
}
