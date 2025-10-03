<?php

namespace App\Listeners;

use App\Events\AppraisalRecordCompleted;
use App\Models\Department;
use App\Notifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendPayrollNotification
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
    public function handle(AppraisalRecordCompleted $event): void
    {
        $payrolls = Department::where('name', 'PAYROLL')->first()->users;

        if (! $payrolls) {
            return;
        }

        Notification::send($payrolls, new Notifications\AppraisalRecordCompleted($event->appraisalRecord));

    }
}
