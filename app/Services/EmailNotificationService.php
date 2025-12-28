<?php

namespace App\Services;

use App\Models\GlobalSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class EmailNotificationService
{
    protected GlobalSetting $settings;

    public function __construct()
    {
        $this->settings = GlobalSetting::first();
    }

    /**
     * Send simple welcome email after registration (no PDFs)
     */
    public function sendSimpleWelcome(User $user): bool
    {
        if (!$this->settings->email_notifications_enabled || !$user->email) {
            return false;
        }

        try {
            Mail::to($user->email)->send(new \App\Mail\SimpleWelcomeEmail($user));
            logger()->info("Simple welcome email sent to user {$user->id}");
            return true;
        } catch (\Exception $e) {
            logger()->error("Failed to send simple welcome email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send full welcome email with Terms and Contract PDFs (after payment)
     */
    public function sendWelcomeEmail(User $user): bool
    {
        if (!$this->settings->email_notifications_enabled) {
            logger()->info("Email notifications disabled. Skipping welcome email for user {$user->id}");
            return false;
        }

        try {
            Mail::to($user->email ?? $user->phone_number . '@temp.local')
                ->send(new \App\Mail\WelcomeEmail($user));

            logger()->info("Welcome email sent successfully to user {$user->id}");
            return true;
        } catch (\Exception $e) {
            logger()->error("Failed to send welcome email to user {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send OTP via email or SMS based on settings
     */
    public function sendOTP(User $user, string $otp): bool
    {
        // If SMS is enabled, prioritize SMS
        if ($this->settings->sms_notifications_enabled && $user->phone_number) {
            return $this->sendSMS($user->phone_number, "Your {$this->settings->app_name} OTP is: {$otp}. Valid for 10 minutes.");
        }

        // Fall back to email if SMS disabled
        if ($this->settings->email_notifications_enabled && $user->email) {
            try {
                Mail::to($user->email)->send(new \App\Mail\OTPEmail($user, $otp));
                return true;
            } catch (\Exception $e) {
                logger()->error("Failed to send OTP email: " . $e->getMessage());
                return false;
            }
        }

        // If neither email nor phone available, ask user to check email/phone
        logger()->warning("No notification method available for user {$user->id}");
        return false;
    }

    /**
     * Send generic notification email
     */
    public function sendNotification(User $user, string $subject, string $message, array $attachments = []): bool
    {
        if (!$this->settings->email_notifications_enabled) {
            return false;
        }

        try {
            Mail::to($user->email ?? $user->phone_number . '@temp.local')
                ->send(new \App\Mail\GenericNotification($user, $subject, $message, $attachments));

            return true;
        } catch (\Exception $e) {
            logger()->error("Failed to send notification email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS (placeholder - integrate with actual SMS provider)
     */
    private function sendSMS(string $phoneNumber, string $message): bool
    {
        // TODO: Integrate with SMS provider (Twilio, Africa's Talking, etc.)
        // For now, log the SMS
        logger()->info("SMS to {$phoneNumber}: {$message}");

        // In production, you would call your SMS API here
        // Example:
        // $client = new \Twilio\Rest\Client($sid, $token);
        // $client->messages->create($phoneNumber, ['from' => $from, 'body' => $message]);

        return true;
    }

    /**
     * Check if email notifications are enabled
     */
    public function isEmailEnabled(): bool
    {
        return $this->settings->email_notifications_enabled ?? false;
    }

    /**
     * Check if SMS notifications are enabled
     */
    public function isSMSEnabled(): bool
    {
        return $this->settings->sms_notifications_enabled ?? false;
    }
}
