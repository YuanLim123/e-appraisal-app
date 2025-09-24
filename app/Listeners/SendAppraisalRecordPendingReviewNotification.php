<?php

namespace App\Listeners;

use App\Mail\AppraisalPendingReviewMail;
use App\Events\AppraisalRecordSubmitted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
