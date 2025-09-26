<?php

namespace App\Http\Controllers\api\V1\User;

use App\Enums\AppraisalRecordStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppraisalRecordResource;
use App\Models\AppraisalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        // List of appraisal records pending the user's review
        $appraisalRecords = AppraisalRecord::query()
            ->where('current_approver_id', auth()->id())
            ->where('status', AppraisalRecordStatus::SUBMITTED)
            ->get();

        return AppraisalRecordResource::collection($appraisalRecords);
    }

    public function show(AppraisalRecord $appraisalRecord)
    {
        Gate::authorize('approveAppraisalRecord', [AppraisalRecord::class, $appraisalRecord]);

        $appraisalRecord->load(['appraiser', 'appraisee']);

        return new AppraisalRecordResource($appraisalRecord);
    }
}
