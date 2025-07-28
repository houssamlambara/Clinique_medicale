<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notification - ' . $this->notification->getTypeLabel(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'notification' => $this->notification,
                'patient' => $this->notification->patient->user,
                'typeLabel' => $this->notification->getTypeLabel(),
                'typeIcon' => $this->notification->getTypeIcon(),
                'typeColor' => $this->notification->getTypeColor(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
