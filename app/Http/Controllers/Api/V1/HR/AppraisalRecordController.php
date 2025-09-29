<?php

namespace App\Http\Controllers\Api\V1\HR;

use App\Models\AppraisalRecord;
use App\Http\Resources\AppraisalRecordResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppraisalRecordController extends Controller
{
    public function index()
    {
        $appraisalRecords = AppraisalRecord::query()
            ->with(['appraiser', 'appraisee', 'currentApprover'])
            ->paginate(10);

        return AppraisalRecordResource::collection($appraisalRecords);
    }
}
