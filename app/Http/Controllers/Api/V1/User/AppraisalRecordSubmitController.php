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


class AppraisalRecordSubmitController extends Controller
{
    use APIResponsesTrait;

    public function __invoke(AppraisalRecord $appraisalRecord, Request $request, AppraisalRecordSubmitService $service)
    {
        $user = User::findOrFail($request->input('appraisee_id'));

        Gate::authorize('update', [AppraisalRecord::class, $appraisalRecord, $user]);

        $service->submit($user, $appraisalRecord);

        return $this->successResponse(message: 'Appraisal record submitted successfully');
    }
}
