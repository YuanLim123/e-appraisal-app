<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppraisalResource;
use App\Models\Appraisal;

class AppraisalController extends Controller
{
    public function index()
    {
        $appraisals = Appraisal::query()
            ->with(['appraiser', 'appraisee', 'approvers', 'approvers.user'])
            ->paginate(10);

        return AppraisalResource::collection($appraisals);
    }

    public function show(Appraisal $appraisal)
    {
        $appraisal->load(['appraiser', 'appraisee', 'approvers', 'approvers.user']);

        return new AppraisalResource($appraisal);
    }
}
