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

class AppraisalRecordAttachmentController extends Controller
{
    public function store(AppraisalRecord $appraisalRecord, Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:1000']
        ]);

        $attachment = $appraisalRecord->addMediaFromRequest('file')->toMediaCollection('attachments');

        return [
            'attachment' => $attachment->getFullUrl(),
        ];
    }

    public function destroy(AppraisalRecord $appraisalRecord)
    {
        $appraisalRecord->clearMediaCollection('attachments');

        return response()->noContent();
    }
}
