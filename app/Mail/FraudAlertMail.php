<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FraudAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $type;
    public $data;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $type (bot_speed_warning, bot_speed_suspension, velocity_abuse, pattern_abuse)
     * @param array $data
     */
    public function __construct(User $user, string $type, array $data = [])
    {
        $this->user = $user;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->getSubject();
        $title = $this->getTitle();
        $message = $this->getMessage();

        return $this->subject($subject)
            ->view('emails.fraud-alert')
            ->with([
                'user' => $this->user,
                'type' => $this->type,
                'title' => $title,
                'message' => $message,
                'messageContent' => $message,
                'incident_data' => $this->data,
            ]);
    }

    private function getSubject(): string
    {
        return match($this->type) {
            'bot_speed_warning' => '⚠️ Warning: Suspicious Task Activity Detected',
            'bot_speed_suspension' => '🚫 Task Access Suspended - Bot-like Behavior',
            'velocity_abuse' => '🚫 Task Access Suspended - Too Many Tasks',
            'pattern_abuse' => '⚠️ Warning: Pattern Manipulation Detected',
            default => '⚠️ Security Alert'
        };
    }

    private function getTitle(): string
    {
        return match($this->type) {
            'bot_speed_warning' => 'First Warning: Task Completed Too Quickly',
            'bot_speed_suspension' => 'Task Access Suspended',
            'velocity_abuse' => 'Velocity Limit Exceeded',
            'pattern_abuse' => 'Pattern Manipulation Warning',
            default => 'Security Alert'
        };
    }

    private function getMessage(): string
    {
        return match($this->type) {
            'bot_speed_warning' => $this->getBotSpeedWarningMessage(),
            'bot_speed_suspension' => $this->getBotSpeedSuspensionMessage(),
            'velocity_abuse' => $this->getVelocityAbuseMessage(),
            'pattern_abuse' => $this->getPatternAbuseMessage(),
            default => 'Suspicious activity has been detected on your account.'
        };
    }

    private function getBotSpeedWarningMessage(): string
    {
        $duration = $this->data['duration'] ?? 0;
        $required = $this->data['required'] ?? 30;

        return "We detected that you completed a task in {$duration} seconds, which is significantly faster than the required minimum of {$required} seconds.\n\n" .
               "This appears to be bot-like behavior and violates our terms of service.\n\n" .
               "⚠️ This is your FIRST WARNING.\n\n" .
               "Please ensure you:\n" .
               "• Complete tasks at a normal human pace\n" .
               "• Read all instructions carefully\n" .
               "• Answer questions honestly\n\n" .
               "Next Offense: 48-hour task suspension";
    }

    private function getBotSpeedSuspensionMessage(): string
    {
        $duration = $this->data['duration'] ?? 0;
        $required = $this->data['required'] ?? 30;
        $banUntil = $this->data['banned_until'] ?? now()->addHours(48);
        $offenseCount = $this->data['offense_count'] ?? 2;

        return "Your task access has been suspended for 48 HOURS due to repeated bot-like behavior.\n\n" .
               "Latest Violation:\n" .
               "• Task completed in: {$duration} seconds\n" .
               "• Required minimum: {$required} seconds\n" .
               "• This is offense #{$offenseCount}\n\n" .
               "🚫 Suspension Details:\n" .
               "• Banned until: {$banUntil->format('M d, Y h:i A')}\n" .
               "• All task access blocked\n" .
               "• Cannot earn during suspension\n\n" .
               "⚠️ FINAL WARNING: One more violation will result in a 7-day suspension requiring manual review.";
    }

    private function getVelocityAbuseMessage(): string
    {
        $tasksCompleted = $this->data['tasks_completed'] ?? 0;
        $limit = $this->data['limit'] ?? 15;
        $banUntil = $this->data['banned_until'] ?? now()->addHours(48);

        return "Your account has been flagged for completing tasks too quickly.\n\n" .
               "Violation Details:\n" .
               "• Tasks completed in last hour: {$tasksCompleted}\n" .
               "• Maximum allowed: {$limit} tasks/hour\n\n" .
               "🚫 Your task access has been SUSPENDED FOR 48 HOURS.\n\n" .
               "Suspension expires: {$banUntil->format('M d, Y h:i A')}\n\n" .
               "This limit exists to prevent automated bots and ensure fair access for all users.";
    }

    private function getPatternAbuseMessage(): string
    {
        return "We've detected suspicious answer patterns in your survey responses.\n\n" .
               "⚠️ Warning: Always selecting the same option or answer patterns that appear automated will result in suspension.\n\n" .
               "Please:\n" .
               "• Read each question carefully\n" .
               "• Provide honest, thoughtful answers\n" .
               "• Avoid rushing through tasks\n\n" .
               "Next offense will result in task suspension.";
    }
}
