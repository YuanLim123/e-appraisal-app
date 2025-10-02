<?php

namespace App\Listeners;

use App\Events\AppraisalRecordSubmitted;
use App\Notifications\AppraisalRecordSubmitted as AppraisalRecordSubmittedNotification;
use Illuminate\Support\Facades\Notification;

class SendAppraiseeNotification
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
        $appraisee = $event->appraisalRecord->appraisee;

        if (! $appraisee) {
            return;
        }

        // send notification to appraisee
        Notification::send($appraisee, new AppraisalRecordSubmittedNotification($event->appraisalRecord));
    }
}
