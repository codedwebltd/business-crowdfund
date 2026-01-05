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

            // Broadcast real-time notification via Pusher
            broadcast(new \App\Events\NotificationSent($user->id, [
                'title' => $this->getTitle($type, $data),
                'message' => $this->getMessage($type, $data),
                'action_url' => '/notifications',
                'action_label' => 'View Notifications',
                'icon' => $this->getIcon($type, $data),
            ]));

            return true;
        } catch (\Exception $e) {
            logger()->error("Database notification failed: " . $e->getMessage());
            return false;
        }
    }

    protected function getTitle(string $type, array $data): string
    {
        return match ($type) {
            'withdrawal_requested' => 'Withdrawal Request Received',
            'withdrawal_processing' => 'Withdrawal Being Processed',
            'withdrawal_approved' => 'Withdrawal Approved',
            'withdrawal_completed' => 'Withdrawal Completed',
            'withdrawal_rejected' => 'Withdrawal Rejected',
            'task_completed' => 'Task Completed',
            'referral_bonus' => 'Referral Bonus Earned',
            'rank_upgraded' => 'Rank Upgraded',
            'balance_matured' => '💰 Balance Available for Withdrawal',
            'testimonial_approved' => '✅ Testimonial Approved!',
            'testimonial_review' => '⏳ Testimonial Under Review',
            'testimonial_rejected' => '❌ Testimonial Rejected',
            'kyc_approved' => '✅ KYC Approved!',
            'kyc_rejected' => '❌ KYC Rejected',
            'kyc_pending_review' => '⏳ KYC Pending Review',
            'burn_rate_alert' => $data['subject'] ?? 'Platform Liquidity Alert',
            default => 'Notification',
        };
    }

    protected function getIcon(string $type, array $data = []): string
    {
        return match ($type) {
            'withdrawal_requested' => '⏳',
            'withdrawal_processing' => '🔄',
            'withdrawal_approved' => '✅',
            'withdrawal_completed' => '💰',
            'withdrawal_rejected' => '❌',
            'task_completed' => '✓',
            'referral_bonus' => '🎉',
            'rank_upgraded' => '⭐',
            'balance_matured' => '💰',
            'testimonial_approved' => '✅',
            'testimonial_review' => '⏳',
            'testimonial_rejected' => '❌',
            'kyc_approved' => '✅',
            'kyc_rejected' => '❌',
            'kyc_pending_review' => '⏳',
            'burn_rate_alert' => match($data['liquidity_status'] ?? 'caution') {
                'healthy' => '✅',
                'caution' => '⚠️',
                'critical' => '🚨',
                'collapse_imminent' => '☠️',
                default => '🔥',
            },
            default => '🔔',
        };
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
            'withdrawal_processing' => "Withdrawal Being Processed - ₦" . number_format($data['amount'] ?? 0),
            'withdrawal_approved' => "Withdrawal Approved - ₦" . number_format($data['amount'] ?? 0),
            'withdrawal_completed' => "Withdrawal Completed - ₦" . number_format($data['amount'] ?? 0),
            'withdrawal_rejected' => "Withdrawal Rejected",
            'task_completed' => "Task Completed - ₦" . number_format($data['amount'] ?? 0) . " Earned",
            'referral_bonus' => "Referral Bonus - ₦" . number_format($data['amount'] ?? 0),
            'rank_upgraded' => "Rank Upgraded to " . ($data['rank'] ?? 'Unknown'),
            'balance_matured' => "Balance Available - ₦" . number_format($data['amount'] ?? 0) . " Ready to Withdraw",
            'testimonial_approved' => "Testimonial Approved!",
            'testimonial_review' => "Testimonial Under Review",
            'testimonial_rejected' => "Testimonial Rejected",
            'kyc_approved' => "KYC Verification Approved!",
            'kyc_rejected' => "KYC Verification Rejected",
            'kyc_pending_review' => "KYC Pending Review",
            'burn_rate_alert' => $data['subject'] ?? 'Platform Liquidity Alert',
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
            'withdrawal_processing' => "Your withdrawal of ₦" . number_format($data['amount'] ?? 0) . " is now being actively processed by our team. Payment will be sent shortly.",
            'withdrawal_approved' => "Great news! Your withdrawal of ₦" . number_format($data['amount'] ?? 0) . " has been approved. Funds will be sent to your " . ($data['payment_method'] ?? 'bank') . " account shortly.",
            'withdrawal_completed' => "Your withdrawal of ₦" . number_format($data['amount'] ?? 0) . " has been successfully processed. Please check your " . ($data['payment_method'] ?? 'bank') . " account.",
            'withdrawal_rejected' => "Your withdrawal request has been rejected. Reason: " . ($data['reason'] ?? 'Not specified') . ". Your balance has been restored.",
            'balance_matured' => "Great news! ₦" . number_format($data['amount'] ?? 0) . " has matured and is now available for withdrawal. " . (($data['count'] ?? 1) > 1 ? "({$data['count']} transactions matured). " : "") . "Your new withdrawable balance is ₦" . number_format($data['withdrawable_balance'] ?? 0) . ". Click 'Withdraw' to request payment now!",
            'testimonial_approved' => "Your testimonial has been automatically approved by our AI system. Thank you for sharing your experience! Please refresh the page to proceed with withdrawal.",
            'testimonial_review' => "Thank you for submitting your testimonial! Our team will review it shortly and get back to you soon. Please check back later.",
            'testimonial_rejected' => "Unfortunately, your testimonial has been rejected. Reason: " . ($data['reason'] ?? 'Not specified') . ". Please submit a different testimonial.",
            'kyc_approved' => "Congratulations! Your KYC verification has been approved. You can now proceed with withdrawals and access all platform features.",
            'kyc_rejected' => "Your KYC verification was rejected. Reason: " . ($data['rejection_reason'] ?? 'Not specified') . ". Please resubmit with correct documents.",
            'kyc_pending_review' => $data['message'] ?? "Your KYC documents are under review. We'll notify you once the verification is complete.",
            'burn_rate_alert' => $data['message'] ?? "Platform liquidity alert for " . ($data['report_date'] ?? 'today') . ". Burn rate: " . ($data['burn_rate'] ?? 0) . ". Status: " . strtoupper($data['liquidity_status'] ?? 'unknown') . ". Please check the admin dashboard for details.",
            default => $data['message'] ?? "You have a new notification.",
        };
    }
}
