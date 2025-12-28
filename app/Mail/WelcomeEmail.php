<?php

namespace App\Mail;

use App\Models\GlobalSetting;
use App\Models\User;
use App\Services\PDFs\TermsAndConditionsPDF;
use App\Services\PDFs\WelcomeContractPDF;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public GlobalSetting $settings;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->settings = GlobalSetting::first();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎉 Welcome to {$this->settings->app_name} - Your Journey Starts Now!",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'user' => $this->user,
                'settings' => $this->settings,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];

        try {
            // Attach Terms and Conditions PDF
            $termsPDF = new TermsAndConditionsPDF($this->user);
            $attachments[] = Attachment::fromData(fn () => $termsPDF->output(), 'Terms-and-Conditions.pdf')
                ->withMime('application/pdf');

            // Attach Welcome Contract PDF
            $contractPDF = new WelcomeContractPDF($this->user);
            $attachments[] = Attachment::fromData(fn () => $contractPDF->output(), 'Welcome-Contract.pdf')
                ->withMime('application/pdf');

        } catch (\Exception $e) {
            logger()->error("Failed to attach PDFs to welcome email: " . $e->getMessage());
        }

        return $attachments;
    }
}
