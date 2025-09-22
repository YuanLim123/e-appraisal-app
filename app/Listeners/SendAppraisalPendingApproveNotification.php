<?php

namespace App\Listeners;

use App\Events\AppraisalRecordSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAppraisalPendingApproveNotification
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

        dd($approver);

    }
}
