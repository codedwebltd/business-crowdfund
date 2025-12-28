<?php

namespace App\Services;

use App\Models\GlobalSetting;
use App\Models\User;
use App\Notifications\WithdrawalNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    protected GlobalSetting $settings;
    protected EmailNotificationService $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->settings = GlobalSetting::first();
        $this->emailService = $emailService;
    }

    /**
     * Central notification dispatcher
     *
     * @param User $user
     * @param string $type (withdrawal_requested, withdrawal_approved, withdrawal_completed, etc)
     * @param array $data
     * @return array Results from each channel
     */
    public function send(User $user, string $type, array $data): array
    {
        $results = [];
        $userPrefs = $user->notification_preferences ?? [];

        // Database notification (always)
        $results['database'] = $this->database($user, $type, $data);

        // Email notification (check global + user preference)
        if ($this->shouldSendEmail($userPrefs)) {
            $results['email'] = $this->email($user, $type, $data);
        }

        // SMS notification (check global + user preference)
        if ($this->shouldSendSms($userPrefs)) {
            $results['sms'] = $this->sms($user, $type, $data);
        }

        // Firebase (check global setting only)
        if ($this->settings->notification_channels['firebase']['enabled'] ?? false) {
            $results['firebase'] = $this->firebase($user, $type, $data);
        }

        // WhatsApp (check global setting only)
        if ($this->settings->notification_channels['whatsapp']['enabled'] ?? false) {
            $results['whatsapp'] = $this->whatsapp($user, $type, $data);
        }

        // Telegram (check global setting only)
        if ($this->settings->notification_channels['telegram']['enabled'] ?? false) {
            $results['telegram'] = $this->telegram($user, $type, $data);
        }

        return $results;
    }

    protected function shouldSendEmail($userPrefs): bool
    {
        // Global switch must be enabled
        if (!($this->settings->email_notifications_enabled || $this->settings->notification_channels['email']['enabled'] ?? false)) {
            return false;
        }
        // User preference must allow email
        return $userPrefs['email'] ?? true;
    }

    protected function shouldSendSms($userPrefs): bool
    {
        // Global switch must be enabled
        if (!$this->settings->sms_notifications_enabled) {
            return false;
        }
        // User preference must allow SMS (if key exists)
        return $userPrefs['sms'] ?? true;
    }

    /**
     * Send database notification
     */
    protected function database(User $user, string $type, array $data): bool
    {
        try {
            $user->notify(new WithdrawalNotification($type, $data));
            return true;
        } catch (\Exception $e) {
            logger()->error("Database notification failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email notification with optional PDF attachment
     */
    protected function email(User $user, string $type, array $data): bool
    {
        if (!$user->email) {
            return false;
        }

        try {
            $subject = $this->getSubject($type, $data);
            $message = $this->getMessage($type, $data);
            $attachments = $data['attachments'] ?? [];

            return $this->emailService->sendNotification($user, $subject, $message, $attachments);
        } catch (\Exception $e) {
            logger()->error("Email notification failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS notification (placeholder)
     */
    protected function sms(User $user, string $type, array $data): bool
    {
        // TODO: Integrate SMS provider
        logger()->info("SMS notification: {$type} to {$user->phone_number}");
        return true;
    }

    /**
     * Firebase push notification (placeholder)
     */
    protected function firebase(User $user, string $type, array $data): bool
    {
        // TODO: Integrate Firebase Cloud Messaging
        logger()->info("Firebase notification: {$type} to user {$user->id}");
        return true;
    }

    /**
     * WhatsApp notification (placeholder)
     */
    protected function whatsapp(User $user, string $type, array $data): bool
    {
        // TODO: Integrate WhatsApp Business API
        logger()->info("WhatsApp notification: {$type} to {$user->phone_number}");
        return true;
    }

    /**
     * Telegram notification (placeholder)
     */
    protected function telegram(User $user, string $type, array $data): bool
    {
        // TODO: Integrate Telegram Bot API
        logger()->info("Telegram notification: {$type} to user {$user->id}");
        return true;
    }

    /**
     * Get notification subject based on type
     */
    protected function getSubject(string $type, array $data): string
    {
        return match ($type) {
            'withdrawal_requested' => "Withdrawal Request Received - ₦" . number_format($data['amount'] ?? 0),
            'withdrawal_approved' => "Withdrawal Approved - ₦" . number_format($data['amount'] ?? 0),
            'withdrawal_completed' => "Withdrawal Completed - ₦" . number_format($data['amount'] ?? 0),
            'withdrawal_rejected' => "Withdrawal Rejected",
            'task_completed' => "Task Completed - ₦" . number_format($data['amount'] ?? 0) . " Earned",
            'referral_bonus' => "Referral Bonus - ₦" . number_format($data['amount'] ?? 0),
            'rank_upgraded' => "Rank Upgraded to " . ($data['rank'] ?? 'Unknown'),
            default => "Notification from {$this->settings->app_name}",
        };
    }

    /**
     * Get notification message based on type
     */
    protected function getMessage(string $type, array $data): string
    {
        return match ($type) {
            'withdrawal_requested' => "Your withdrawal request for ₦" . number_format($data['amount'] ?? 0) . " has been received and is being processed. You will receive payment within " . ($this->settings->withdrawal_processing_times[$data['rank'] ?? 'bronze'] ?? '48-72 hours') . ".",
            'withdrawal_approved' => "Great news! Your withdrawal of ₦" . number_format($data['amount'] ?? 0) . " has been approved. Funds will be sent to your " . ($data['payment_method'] ?? 'bank') . " account shortly.",
            'withdrawal_completed' => "Your withdrawal of ₦" . number_format($data['amount'] ?? 0) . " has been successfully processed. Please check your " . ($data['payment_method'] ?? 'bank') . " account.",
            'withdrawal_rejected' => "Your withdrawal request has been rejected. Reason: " . ($data['reason'] ?? 'Not specified') . ". Your balance has been restored.",
            default => $data['message'] ?? "You have a new notification.",
        };
    }
}
