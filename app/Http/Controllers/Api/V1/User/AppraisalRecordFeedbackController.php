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

class AppraisalRecordFeedbackController extends Controller
{
    public function __invoke(AppraisalRecord $appraisalRecord, AppraisalRecordFeedbackRequest $request, AppraisalRecordFeedbackService $service)
    {
        $user = User::findOrFail($request->input('appraisee_id'));

        Gate::authorize('update', [AppraisalRecord::class, $appraisalRecord, $user]);

        // get isSubmit query param, default to false
        $isSubmit = $request->query('isSubmit', false);

        $appraisalRecord = $service->store($user, $appraisalRecord, $request->validated(), $isSubmit);

        return new AppraisalRecordResource($appraisalRecord);
    }
}
