<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppraisalRecordFeedbackRequest;
use App\Http\Requests\StoreAppraisalRecordRequest;
use App\Http\Resources\AppraisalRecordResource;
use App\Models\AppraisalRecord;
use App\Models\User;
use App\Services\V1\User\AppraisalRecordFeedbackService;
use App\Services\V1\User\AppraisalRecordService;
use App\Services\V1\User\AppraisalRecordSubmitService;
use App\Traits\APIResponsesTrait;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class AppraisalRecordController extends Controller
{
    use APIResponsesTrait;

    public function index()
    {
        $appraisalRecords = AppraisalRecord::query()
            ->with(['appraiser', 'appraisee'])
            ->paginate(10);

        return AppraisalRecordResource::collection($appraisalRecords);
    }

    public function store(StoreAppraisalRecordRequest $request, AppraisalRecordService $service)
    {
        $user = User::findOrFail($request->input('appraisee_id'));

        Gate::authorize('create', [AppraisalRecord::class, $user->appraisalAsAppraisee]);
        
        $appraisalRecord = $service->store($user, $request->validated());

        // return new AppraisalRecordResource($appraisalRecord);
        return response()->json([
            'message' => 'Appraisal record created successfully.',
        ], 201);
    }

    public function update(AppraisalRecord $appraisalRecord, StoreAppraisalRecordRequest $request, AppraisalRecordService $service)
    {
        $user = User::findOrFail($request->input('appraisee_id'));
        
        Gate::authorize('update', [AppraisalRecord::class, $appraisalRecord, $user]);

        $appraisalRecord = $service->update($user, $appraisalRecord, $request->validated());

        return new AppraisalRecordResource($appraisalRecord);
    }
}
