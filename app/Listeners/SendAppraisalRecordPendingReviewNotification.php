<?php

namespace App\Listeners;

use App\Events\AppraisalRecordSubmitted;
use App\Mail\AppraisalPendingReviewMail;
use Illuminate\Support\Facades\Mail;

class SendAppraisalRecordPendingReviewNotification
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
    public function handle(AppraisalRecordSubmitted $event): void
    {
        $approver = $event->appraisalRecord->approvers()->with('user')->first()?->user;

        if (! $approver || ! $approver->email) {
            return;
        }

        Mail::to($approver)->send(new AppraisalPendingReviewMail($event->appraisalRecord));
    }
}
