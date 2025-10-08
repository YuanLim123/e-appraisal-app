<?php

namespace App\Listeners;

use App\Events\AppraisalRecordRejected;
use App\Notifications\AppraisalRecordRejected as AppraisalRecordRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendAppraiserNotification 
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
    public function handle(AppraisalRecordRejected $event): void
    {
        $appraiser = $event->appraisalRecord->appraiser;

        if (! $appraiser) {
            return;
        }

        // send notification to appraisee
        Notification::send($appraiser, new AppraisalRecordRejectedNotification($event->appraisalRecord));
    }
}
