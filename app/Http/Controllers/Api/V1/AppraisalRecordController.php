<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AppraisalRecordPurposeType;
use App\Enums\AppraisalRecordStatus;
use App\Enums\AppraisalRecordType;
use App\Models\AppraisalRecord;
use App\Models\User;
use App\Models\Season;
use App\Http\Requests\StoreAppraisalRecordRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppraisalRecordResource;
use App\Services\V1\AppraisalRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
        Gate::authorize('create', [AppraisalRecord::class, $user->appraisalAsAppraisee]);

        try {
            $appraisalRecord = $service->store($user, $request->validated());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
        return new AppraisalRecordResource($appraisalRecord);
    }

    public function update(User $user, AppraisalRecord $appraisalRecord, StoreAppraisalRecordRequest $request, AppraisalRecordService $service)
    {
        Gate::authorize('update', [AppraisalRecord::class, $appraisalRecord]);

        // check if season is valid

        // check rating sum

        // calculateGrade

        try {
            $appraisalRecord = $service->update($user, $appraisalRecord, $request->validated());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
        return new AppraisalRecordResource($appraisalRecord);

    }
}
