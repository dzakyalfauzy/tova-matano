<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VolunteerRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $fullName,
        public string $registrationCode,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran Relawan TMCC - ' . $this->registrationCode,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.volunteer-registration',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
