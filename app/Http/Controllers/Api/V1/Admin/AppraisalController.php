<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppraisalRequest;
use App\Http\Requests\UpdateAppraisalRequest;
use App\Http\Resources\AppraisalResource;
use App\Models\Appraisal;
use App\Models\User;
use App\Services\V1\Admin\AppraisalService;

class AppraisalController extends Controller
{
    public function store(User $user, StoreAppraisalRequest $request, AppraisalService $appraisalService)
    {
        if (Appraisal::where('appraisee_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'The selected user has already been registered as an appraisee.',
            ], 422);
        }

        $appraisal = $appraisalService->store($user, $request->validated());

        return new AppraisalResource($appraisal);
    }

    public function update(Appraisal $appraisal, UpdateAppraisalRequest $request, AppraisalService $appraisalService)
    {

        $appraisal = $appraisalService->update($appraisal, $request->validated());

        return new AppraisalResource($appraisal);
    }
}
