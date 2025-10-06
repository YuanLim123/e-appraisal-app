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

        return new AppraisalRecordResource($appraisalRecord);
    }

    public function update(AppraisalRecord $appraisalRecord, StoreAppraisalRecordRequest $request, AppraisalRecordService $service)
    {
        $user = User::findOrFail($request->input('appraisee_id'));
        
        Gate::authorize('update', [AppraisalRecord::class, $appraisalRecord, $user]);

        $appraisalRecord = $service->update($user, $appraisalRecord, $request->validated());

        return new AppraisalRecordResource($appraisalRecord);
    }

    public function storeFeedback(AppraisalRecord $appraisalRecord, AppraisalRecordFeedbackRequest $request, AppraisalRecordFeedbackService $service)
    {
        $user = User::findOrFail($request->input('appraisee_id'));

        Gate::authorize('update', [AppraisalRecord::class, $appraisalRecord, $user]);

        // get isSubmit query param, default to false
        $isSubmit = $request->query('isSubmit', false);

        $appraisalRecord = $service->store($user, $appraisalRecord, $request->validated(), $isSubmit);

        return new AppraisalRecordResource($appraisalRecord);
    }

    public function submit(AppraisalRecord $appraisalRecord, Request $request, AppraisalRecordSubmitService $service)
    {
        $user = User::findOrFail($request->input('appraisee_id'));

        Gate::authorize('update', [AppraisalRecord::class, $appraisalRecord, $user]);

        $service->submit($user, $appraisalRecord);

        return $this->successResponse(message: 'Appraisal record submitted successfully');

    }
}
