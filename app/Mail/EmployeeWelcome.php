<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $employeeName,
        public string $username,
        public string $password,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to the shop');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.employee-welcome');
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        return [];
    }
}
