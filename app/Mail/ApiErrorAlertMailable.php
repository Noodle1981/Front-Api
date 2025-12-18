<?php

namespace App\Mail;

use App\Models\ClientCredential;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApiErrorAlertMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ClientCredential $credential,
        public array $errorData
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Alerta: Error en API - ' . $this->credential->apiService->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.api-error-alert',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
