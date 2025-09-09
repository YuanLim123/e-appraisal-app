<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\AppraisalControl;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppraisalControlResource;
use Illuminate\Http\Request;

class AppraisalControlController extends Controller
{
    public function index()
    {
        $appraisalControl = AppraisalControl::query()->paginate(10);

        $appraisalControl->load(['appraiser', 'appraisee', 'approvers', 'approvers.user']);

        return AppraisalControlResource::collection($appraisalControl);
    }


}
