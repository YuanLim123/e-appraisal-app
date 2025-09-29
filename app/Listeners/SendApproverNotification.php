<?php

namespace App\Listeners;

use App\Events\AppraisalRecordSubmitted;
use App\Events\ApprovalProceeded;
use App\Mail\AppraisalRecordPendingReviewMail;
use Illuminate\Support\Facades\Mail;

class SendApproverNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ApprovalProceeded|AppraisalRecordSubmitted $event): void
    {
        $approver = $event->appraisalRecord->currentApprover;

        if (! $approver || ! $approver->email) {
            return;
        }

        Mail::to($approver)->send(new AppraisalRecordPendingReviewMail($event->appraisalRecord));
    }
}
