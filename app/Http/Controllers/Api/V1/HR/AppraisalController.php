<?php

namespace App\Http\Controllers\Api\V1\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppraisalRequest;
use App\Http\Requests\UpdateAppraisalRequest;
use App\Http\Resources\AppraisalResource;
use App\Models\Appraisal;
use App\Models\User;
use App\Services\V1\HR\AppraisalService;

class AppraisalController extends Controller
{
    public function index()
    {
        $appraisals = Appraisal::query()
            ->with(['appraiser', 'appraisee.departments', 'approvers', 'approvers.user'])
            ->paginate(10);

        return AppraisalResource::collection($appraisals);
    }

    public function show(Appraisal $appraisal)
    {
        $appraisal->load(['appraiser', 'appraisee.departments', 'approvers', 'approvers.user']);

        return new AppraisalResource($appraisal);
    }

    public function store(StoreAppraisalRequest $request, AppraisalService $appraisalService)
    {
        if (Appraisal::where('appraisee_id', $request->validated()['appraisee_id'])->exists()) {
            return response()->json([
                'message' => 'The selected user has already been registered as an appraisee.',
            ], 422);
        }

        $appraisalService->store($request->validated());

        return response()->json([
            'message' => 'Appraisal created successfully.',
        ], 201);
    }

    public function update(Appraisal $appraisal, UpdateAppraisalRequest $request, AppraisalService $appraisalService)
    {
        $appraisal = $appraisalService->update($appraisal, $request->validated());

        return new AppraisalResource($appraisal);
    }
}
