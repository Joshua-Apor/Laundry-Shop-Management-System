<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderReadyForPickup extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $customerName,
        public int $orderId,
        public float $remainingBalance,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your laundry order #{$this->orderId} is ready for pickup",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-ready-for-pickup',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
