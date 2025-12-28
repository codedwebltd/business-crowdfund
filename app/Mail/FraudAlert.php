<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FraudAlert extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $type;
    public string $title;
    public string $messageContent;
    public ?array $vpn_data;

    public function __construct(User $user, string $type, string $title, string $messageContent, ?array $vpn_data = null)
    {
        $this->user = $user;
        $this->type = $type;
        $this->title = $title;
        $this->messageContent = $messageContent;
        $this->vpn_data = $vpn_data;
    }

    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'vpn_warning' => '⚠️ Security Alert: VPN Detected',
            'vpn_suspension' => '⚠️ Task Access Suspended - VPN Detected',
            'vpn_ban' => '🚫 Account Suspended - VPN Abuse',
            default => 'Security Alert',
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.fraud-alert',
            with: [
                'user' => $this->user,
                'type' => $this->type,
                'title' => $this->title,
                'message' => $this->messageContent,
                'vpn_data' => $this->vpn_data,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
