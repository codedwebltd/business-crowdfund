<?php

namespace App\Mail;

use App\Models\GlobalSetting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class GenericNotification extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public GlobalSetting $settings;
    public string $emailSubject;
    public string $message;
    public array $emailAttachments;

    public function __construct(User $user, string $subject, string $message, array $attachments = [])
    {
        $this->user = $user;
        $this->emailSubject = $subject;
        $this->message = $message;
        $this->emailAttachments = $attachments;
        $this->settings = GlobalSetting::first();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'user' => $this->user,
                'settings' => $this->settings,
                'messageContent' => $this->message,
            ],
        );
    }

    public function attachments(): array
    {
        return $this->emailAttachments;
    }
}
