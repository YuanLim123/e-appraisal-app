<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AppraisalRecordPurposeType;
use App\Enums\AppraisalRecordStatus;
use App\Enums\AppraisalRecordType;
use App\Models\User;
use App\Models\Season;
use App\Http\Requests\StoreAppraisalRecordRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppraisalRecordResource;
use App\Models\AppraisalRecord;
use App\Services\V1\AppraisalRecordService;
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

    public function store(User $user, StoreAppraisalRecordRequest $request, AppraisalRecordService $service)
    {
        try {
            $appraisalRecord = $service->store($user, $request->validated());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return new AppraisalRecordResource($appraisalRecord);
    }
}
