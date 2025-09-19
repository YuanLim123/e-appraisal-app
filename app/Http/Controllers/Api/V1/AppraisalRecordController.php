<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppraisalRecordRequest;
use App\Http\Resources\AppraisalRecordResource;
use App\Models\AppraisalRecord;
use App\Models\User;
use App\Services\V1\AppraisalRecordService;
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

        $appraisalRecord = $service->store($user, $request->validated());

        return new AppraisalRecordResource($appraisalRecord);
    }

    public function update(User $user, AppraisalRecord $appraisalRecord, StoreAppraisalRecordRequest $request, AppraisalRecordService $service)
    {
        Gate::authorize('update', [AppraisalRecord::class, $appraisalRecord]);

        $appraisalRecord = $service->update($user, $appraisalRecord, $request->validated());

        return new AppraisalRecordResource($appraisalRecord);
    }
}
