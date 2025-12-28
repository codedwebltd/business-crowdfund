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
            'withdrawal_requested' => 'Withdrawal Request Received',
            'withdrawal_approved' => 'Withdrawal Approved',
            'withdrawal_completed' => 'Withdrawal Completed',
            'withdrawal_rejected' => 'Withdrawal Rejected',
            'task_completed' => 'Task Completed',
            'referral_bonus' => 'Referral Bonus Earned',
            'rank_upgraded' => 'Rank Upgraded',
            default => 'Notification',
        };
    }

    protected function getMessage(): string
    {
        return match ($this->type) {
            'withdrawal_requested' => "Your withdrawal of ₦" . number_format($this->data['amount'] ?? 0) . " is being processed.",
            'withdrawal_approved' => "Your withdrawal of ₦" . number_format($this->data['amount'] ?? 0) . " has been approved!",
            'withdrawal_completed' => "₦" . number_format($this->data['amount'] ?? 0) . " has been sent to your account.",
            'withdrawal_rejected' => "Your withdrawal was rejected. Balance restored.",
            default => $this->data['message'] ?? '',
        };
    }

    protected function getIcon(): string
    {
        return match ($this->type) {
            'withdrawal_requested' => '⏳',
            'withdrawal_approved' => '✅',
            'withdrawal_completed' => '💰',
            'withdrawal_rejected' => '❌',
            'task_completed' => '✓',
            'referral_bonus' => '🎉',
            'rank_upgraded' => '⭐',
            default => '🔔',
        };
    }
}
