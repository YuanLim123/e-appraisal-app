<?php

namespace App\Mail;

use App\Models\Appraisal;
use App\Models\AppraisalRecord;
use App\Models\RecordApprover;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppraisalPendingReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public AppraisalRecord $appraisalRecord,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appraisal Form Review Pending',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $deparments = $this->appraisalRecord?->appraisee?->departments->pluck('name');
        $url = 'URL'; // need to wait for creating endpoint for review
        $currentApproverName = $this->appraisalRecord?->currentApprover?->full_name;

        return new Content(
            markdown: 'mail.appraisal.review_pending',
            with: [
                'appraisee' => $this->appraisalRecord->appraisee,
                'departments' => $deparments,
                'currentApprover' => $currentApproverName,
                'season' => $this->appraisalRecord->season->name,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
