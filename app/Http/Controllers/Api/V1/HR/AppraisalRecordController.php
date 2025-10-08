<?php

namespace App\Http\Controllers\Api\V1\HR;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppraisalRecordResource;
use App\Models\AppraisalRecord;

class AppraisalRecordController extends Controller
{
    public function index()
    {
        $appraisalRecords = AppraisalRecord::query()
            ->with(['appraiser', 'appraisee', 'currentApprover', 'approvers.user'])
            ->paginate(10);

        return AppraisalRecordResource::collection($appraisalRecords);
    }
}
