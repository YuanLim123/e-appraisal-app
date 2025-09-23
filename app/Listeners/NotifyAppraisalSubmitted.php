<?php

namespace App\Listeners;

use App\Events\AppraisalRecordSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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

        if(empty($appraisee)){
            return;
        }

        // send notification to appraisee
    }
}
