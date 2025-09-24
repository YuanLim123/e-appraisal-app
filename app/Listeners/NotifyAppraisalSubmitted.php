<?php

namespace App\Listeners;

use App\Events\AppraisalRecordSubmitted;

class NotifyAppraisalSubmitted
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
        $appraisee = $event->appraisalRecord?->appraisee;

        if (! $appraisee) {
            return;
        }

        // send notification to appraisee
    }
}
