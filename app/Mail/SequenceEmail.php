<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\SequenceEnrollment;
use App\Models\SequenceStep;

class SequenceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $enrollment;
    public $step;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SequenceEnrollment $enrollment, SequenceStep $step)
    {
        $this->enrollment = $enrollment;
        $this->step = $step;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->step->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.sequence',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
