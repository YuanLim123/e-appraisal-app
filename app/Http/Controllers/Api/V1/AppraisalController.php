<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Appraisal;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppraisalResource;
use Illuminate\Http\Request;

class AppraisalController extends Controller
{
    public function index()
    {
        $appraisals = Appraisal::query()->paginate(10);

        $appraisals->load(['appraiser', 'appraisee', 'approvers', 'approvers.user']);

        return AppraisalResource::collection($appraisals);
    }


}
