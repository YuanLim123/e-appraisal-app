<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Requests\StoreAppraisalRequest;
use App\Models\User;
use App\Models\Appraisal;
use App\Http\Resources\AppraisalResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppraisalController extends Controller
{
    public function store(User $user, StoreAppraisalRequest $request)
    {
        if (Appraisal::where('appraisee_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'The selected user has already been registered as an appraisee.',
            ], 422);
        }

        $attributes = $request->validated();

        $appraisal = $user->appraisalAsAppraisee()
            ->create([
                'appraiser_id' => $attributes['appraiser_id'],
            ]);

        $appraisal->approvers()->createMany($attributes['approvers']);


        // validate input
        // create appraisal
        // $appraisal = Appraisal::create([
        //     'appraisee_id' => $user->id,
        //     'appraiser_id' => $attributes['appraiser_id'],
        // ]);

        // create approver roles
        //$appraisal->approvers()->createMany($attributes['approvers']);

        // return resource
        $appraisal->load(['appraiser', 'appraisee', 'approvers', 'approvers.user']);

        return new AppraisalResource($appraisal);
    }
}
