<?php

namespace App\Notifications;

use App\Models\AppraisalRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppraisalRecordCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public AppraisalRecord $appraisalRecord)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = 'test';
        $appraisee = $this->appraisalRecord->appraisee->full_name ?? 'The Appraisee';
        return (new MailMessage)
            ->subject('Appraisal Record Approved')
            ->greeting('Notification')
            ->line($appraisee . '\'s appraisal form has been approved')
            ->action('Click here', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appraisal_record_id' => $this->appraisalRecord->id,
            'appraiser_id' => $this->appraisalRecord->appraiser_id,
            'appraisee_id' => $this->appraisalRecord->appraisee_id,
            'season_id' => $this->appraisalRecord->season_id,
            'remark' => 'Appraisal Record Approved',
        ];
    }
}
