<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WithdrawalNotification extends Notification
{
    use Queueable;

    protected string $type;
    protected array $data;

    public function __construct(string $type, array $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'data' => $this->data,
            'icon' => $this->getIcon(),
        ];
    }

    protected function getTitle(): string
    {
        return match ($this->type) {
            'account_activated' => '🎉 Account Activated!',
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
            'burn_rate_alert' => $this->data['subject'] ?? 'Platform Liquidity Alert',
            'star_rating_promoted' => '🎉 Star Rating Promoted!' . (($this->data['new_stars'] ?? 0) == 5 ? ' 👑' : ''),
            'star_rating_demoted' => '⚠️ Star Rating Update',
            'payment_rejected' => '❌ Payment Rejected',
            'plan_upgrade_available' => '🎁 Plan Upgrade Available!',
            'new_support_ticket' => '🎫 New Support Ticket',
            'new_support_message' => '💬 New Support Message',
            default => 'Notification',
        };
    }

    protected function getMessage(): string
    {
        return match ($this->type) {
            'account_activated' => "Congratulations! Your account has been successfully activated with the " . ($this->data['plan_name'] ?? 'Premium') . " plan. Your payment of ₦" . number_format($this->data['amount'] ?? 0, 2) . " has been confirmed. You can now access all platform features and start earning!",
            'withdrawal_requested' => "Your withdrawal of ₦" . number_format($this->data['amount'] ?? 0) . " is being processed.",
            'withdrawal_processing' => "Your withdrawal of ₦" . number_format($this->data['amount'] ?? 0) . " is now being actively processed by our team.",
            'withdrawal_approved' => "Your withdrawal of ₦" . number_format($this->data['amount'] ?? 0) . " has been approved!",
            'withdrawal_completed' => "₦" . number_format($this->data['amount'] ?? 0) . " has been sent to your account.",
            'withdrawal_rejected' => "Your withdrawal was rejected. Balance restored.",
            'task_completed' => "You've completed a task and earned ₦" . number_format($this->data['amount'] ?? 0),
            'referral_bonus' => "You earned ₦" . number_format($this->data['amount'] ?? 0) . " from referral commissions!",
            'rank_upgraded' => "Congratulations! You've been promoted to " . ($this->data['new_rank'] ?? 'new rank') . "!",
            'balance_matured' => "Great news! ₦" . number_format($this->data['amount'] ?? 0) . " has matured and is now available for withdrawal.",
            'testimonial_approved' => "Your testimonial has been automatically approved by our AI system. Thank you for sharing your experience! Please refresh the page to proceed with withdrawal.",
            'testimonial_review' => "Thank you for submitting your testimonial! Our team will review it shortly and get back to you soon. Please check back later.",
            'testimonial_rejected' => "Unfortunately, your testimonial has been rejected. Reason: " . ($this->data['reason'] ?? 'Not specified') . ". Please submit a different testimonial.",
            'kyc_approved' => "Your KYC verification has been approved! You can now access all platform features.",
            'kyc_rejected' => "Your KYC verification was rejected. Reason: " . ($this->data['reason'] ?? 'Not specified'),
            'kyc_pending_review' => "Your KYC documents have been submitted and are pending review.",
            'burn_rate_alert' => $this->data['message'] ?? 'Platform liquidity alert',
            'star_rating_promoted' => "🎉 Congratulations! Your performance rating has been upgraded to " . ($this->data['new_stars'] ?? 0) . " stars! " . (($this->data['new_stars'] ?? 0) == 5 ? "You're now a 5-star General with HIGHEST withdrawal priority! 👑" : "Keep up the great work!"),
            'star_rating_demoted' => "⚠️ Your performance rating has been adjusted to " . ($this->data['new_stars'] ?? 0) . " stars. " . ($this->data['reason'] ?? 'Stay active to improve your rating!'),
            'payment_rejected' => "Your activation payment has been rejected. Reason: " . ($this->data['reason'] ?? 'Not specified') . ". Please contact support or submit a new payment proof.",
            'plan_upgrade_available' => "🎁 Congratulations! You now qualify for the " . ($this->data['qualified_plan_name'] ?? 'Premium') . " plan with a special " . ($this->data['discount_percentage'] ?? 20) . "% discount!",
            'new_support_ticket' => "🎫 New support ticket #" . ($this->data['ticket_number'] ?? '') . " from " . ($this->data['from'] ?? 'a user') . ". Subject: " . ($this->data['subject'] ?? 'Support Request') . ". Please respond promptly.",
            'new_support_message' => "💬 New message in ticket #" . ($this->data['ticket_number'] ?? '') . " from " . ($this->data['from'] ?? 'user') . ": \"" . ($this->data['message_preview'] ?? 'New message') . "\"",
            default => $this->data['message'] ?? '',
        };
    }

    protected function getIcon(): string
    {
        return match ($this->type) {
            'account_activated' => '🎉',
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
            'burn_rate_alert' => '⚠️',
            'star_rating_promoted' => (($this->data['new_stars'] ?? 0) == 5 ? '👑' : '⭐'),
            'star_rating_demoted' => '📉',
            'payment_rejected' => '❌',
            'plan_upgrade_available' => '🎁',
            'new_support_ticket' => '🎫',
            'new_support_message' => '💬',
            default => '🔔',
        };
    }
}
