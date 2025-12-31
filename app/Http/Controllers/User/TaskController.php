<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserTask;
use App\Models\Transaction;
use App\Models\FraudIncident;
use App\Models\DeviceFingerprint;
use App\Models\CommissionLedger;
use App\Models\ReferralTree;
use App\Models\GlobalSetting;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\FraudAlertMail;

class TaskController extends Controller
{
    public function start($id)
    {
        $task = UserTask::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Mark as in progress if pending
        if ($task->status === 'PENDING') {
            $task->update([
                'status' => 'IN_PROGRESS',
                'started_at' => now()
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function complete(Request $request, $id, RecaptchaService $recaptcha)
    {
        $validated = $request->validate([
            'response_data' => 'required|array',
            'duration' => 'required|integer|min:0',
            'recaptcha_token' => 'nullable|string'
        ]);

        $task = UserTask::with('taskTemplate')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Verify task can be completed
        if ($task->status === 'COMPLETED') {
            return back()->withErrors(['error' => 'Task already completed']);
        }

        if ($task->status === 'EXPIRED' || $task->expires_at < now()) {
            return back()->withErrors(['error' => 'Task has expired']);
        }

        // === FRAUD DETECTION & BAN ENFORCEMENT ===
        $user = auth()->user();
        $settings = \App\Models\GlobalSetting::first();

        // Check reCAPTCHA if required
        if ($recaptcha->isEnabled() && $recaptcha->shouldTriggerOnFraud()) {
            // Check if user has recent fraud incidents requiring CAPTCHA
            $recentFraudCount = FraudIncident::where('user_id', $user->id)
                ->where('created_at', '>', now()->subDays(7))
                ->count();

            if ($recentFraudCount > 0 || $request->has('recaptcha_token')) {
                $token = $validated['recaptcha_token'] ?? null;

                if (!$token) {
                    return back()->withErrors([
                        'error' => 'CAPTCHA verification required',
                        'require_captcha' => true
                    ]);
                }

                if (!$recaptcha->verify($token)) {
                    return back()->withErrors([
                        'error' => 'CAPTCHA verification failed. Please try again.',
                        'require_captcha' => true
                    ]);
                }
            }
        }

        // Check 1: Is user currently banned?
        if ($user->task_ban_until && $user->task_ban_until > now()) {
            $banEnd = $user->task_ban_until->format('M d, Y h:i A');
            return back()->withErrors([
                'error' => "🚫 Task Rejected! Your account is suspended until {$banEnd}. This task will NOT be credited and your submission has been discarded. Contact support if you believe this is an error."
            ]);
        }

        // Check 2: Bot Speed Detection (task completed too fast)
        $duration = $validated['duration'];
        $minTime = $task->taskTemplate->completion_time_seconds ?? 30;

        if ($duration < $minTime) {
            // Get offense count
            $offenseCount = FraudIncident::where('user_id', $user->id)
                ->where('incident_type', 'BOT_SPEED')
                ->where('created_at', '>', now()->subDays(30))
                ->count();

            // Progressive penalty
            if ($offenseCount == 0) {
                // First offense: Warning only
                FraudIncident::create([
                    'user_id' => $user->id,
                    'incident_type' => 'BOT_SPEED',
                    'severity' => 'MEDIUM',
                    'incident_data' => [
                        'task_id' => $task->id,
                        'duration' => $duration,
                        'required_minimum' => $minTime,
                        'completion_percentage' => round(($duration / $minTime) * 100) . '%'
                    ],
                    'action_taken' => 'WARNING',
                    'banned_until' => null,
                ]);

                // Send warning email
                Mail::to($user->email)->send(new FraudAlertMail($user, 'bot_speed_warning', [
                    'duration' => $duration,
                    'required' => $minTime,
                    'offense_count' => 1
                ]));

                return back()->withErrors([
                    'error' => "⚠️ Warning: Task completed too quickly ({$duration}s, minimum {$minTime}s). This is your first warning. Next offense will result in suspension."
                ]);

            } else {
                // 2nd+ offense: Ban for 48 hours
                $banUntil = now()->addHours(48);

                FraudIncident::create([
                    'user_id' => $user->id,
                    'incident_type' => 'BOT_SPEED',
                    'severity' => 'HIGH',
                    'incident_data' => [
                        'task_id' => $task->id,
                        'duration' => $duration,
                        'required_minimum' => $minTime,
                        'offense_number' => $offenseCount + 1
                    ],
                    'action_taken' => 'TASK_BANNED_48HRS',
                    'banned_until' => $banUntil,
                ]);

                $user->update(['task_ban_until' => $banUntil]);

                // Send suspension email
                Mail::to($user->email)->send(new FraudAlertMail($user, 'bot_speed_suspension', [
                    'duration' => $duration,
                    'required' => $minTime,
                    'banned_until' => $banUntil,
                    'offense_count' => $offenseCount + 1
                ]));

                return back()->withErrors([
                    'error' => "🚫 Task access suspended for 48 hours due to repeated bot-like behavior. Ban expires: {$banUntil->format('M d, Y h:i A')}"
                ]);
            }
        }

        // Check 3: Velocity Abuse (max tasks per hour from settings)
        $maxTasksPerHour = $settings->fraud_detection_rules['max_tasks_per_hour'] ?? 15; // Fallback to 15

        $tasksLastHour = UserTask::where('user_id', $user->id)
            ->where('status', 'COMPLETED')
            ->where('completed_at', '>', now()->subHour())
            ->count();

        if ($tasksLastHour >= $maxTasksPerHour) {
            $banUntil = now()->addHours(48);

            FraudIncident::create([
                'user_id' => $user->id,
                'incident_type' => 'VELOCITY_ABUSE',
                'severity' => 'HIGH',
                'incident_data' => [
                    'tasks_last_hour' => $tasksLastHour,
                    'limit' => $maxTasksPerHour,
                    'current_task_id' => $task->id
                ],
                'action_taken' => 'TASK_BANNED_48HRS',
                'banned_until' => $banUntil,
            ]);

            $user->update(['task_ban_until' => $banUntil]);

            // Send velocity abuse email
            Mail::to($user->email)->send(new FraudAlertMail($user, 'velocity_abuse', [
                'tasks_completed' => $tasksLastHour,
                'limit' => $maxTasksPerHour,
                'banned_until' => $banUntil
            ]));

            return back()->withErrors([
                'error' => "🚫 Too many tasks completed in 1 hour ({$tasksLastHour}/{$maxTasksPerHour} max). Suspended for 48 hours until {$banUntil->format('M d, Y h:i A')}"
            ]);
        }

        // Check 4: Device Fingerprint - Multi-Account Detection
        if ($request->has('device_fingerprint')) {
            $fingerprintHash = $request->input('device_fingerprint');

            // Check if this device is used by multiple accounts
            $deviceUsers = DeviceFingerprint::where('fingerprint_hash', $fingerprintHash)
                ->distinct('user_id')
                ->count();

            if ($deviceUsers > 1) {
                // Get all users using this device
                $otherUsers = DeviceFingerprint::where('fingerprint_hash', $fingerprintHash)
                    ->where('user_id', '!=', $user->id)
                    ->with('user:id,email,name')
                    ->get();

                // Create fraud incident
                FraudIncident::create([
                    'user_id' => $user->id,
                    'incident_type' => 'DEVICE_SHARING',
                    'severity' => 'HIGH',
                    'incident_data' => [
                        'fingerprint_hash' => $fingerprintHash,
                        'device_user_count' => $deviceUsers,
                        'other_accounts' => $otherUsers->pluck('user.email')->toArray(),
                        'task_id' => $task->id
                    ],
                    'action_taken' => 'WARNING_DEVICE_SHARED',
                    'banned_until' => null,
                ]);

                return back()->withErrors([
                    'error' => "⚠️ Multi-account detected! This device is linked to {$deviceUsers} accounts. Using multiple accounts is against our Terms of Service and may result in permanent ban."
                ]);
            }
        }

        // Check 5: Pattern Abuse (for SURVEY tasks)
        if ($task->taskTemplate->category === 'SURVEY') {
            $answers = $validated['response_data']['answers'] ?? [];

            // Check if all answers are the same (e.g., always selecting first option)
            if (!empty($answers)) {
                $firstAnswer = reset($answers);
                $allSame = true;

                foreach ($answers as $answer) {
                    if ($answer !== $firstAnswer) {
                        $allSame = false;
                        break;
                    }
                }

                if ($allSame && count($answers) >= 3) {
                    FraudIncident::create([
                        'user_id' => $user->id,
                        'incident_type' => 'PATTERN_ABUSE',
                        'severity' => 'MEDIUM',
                        'incident_data' => [
                            'task_id' => $task->id,
                            'pattern' => 'always_same_option',
                            'selected_option' => $firstAnswer,
                            'questions_count' => count($answers)
                        ],
                        'action_taken' => 'WARNING',
                        'banned_until' => null,
                    ]);

                    Mail::to($user->email)->send(new FraudAlertMail($user, 'pattern_abuse', [
                        'pattern' => 'always_same_option',
                        'questions' => count($answers)
                    ]));

                    return back()->withErrors([
                        'error' => '⚠️ Warning: Suspicious answer pattern detected. Please read each question carefully and provide honest answers. Repeated violations will result in suspension.'
                    ]);
                }
            }
        }

        // Check 6: Video Watch Time Validation (for VIDEO tasks)
        if ($task->taskTemplate->category === 'VIDEO') {
            $watchedSeconds = $validated['response_data']['watched_seconds'] ?? 0;
            $requiredSeconds = $validated['response_data']['required_seconds'] ?? 0;
            $completionPercentage = $validated['response_data']['completion_percentage'] ?? 0;

            // Require 90% minimum watch time
            if ($completionPercentage < 90 || $watchedSeconds < ($requiredSeconds * 0.9)) {
                return back()->withErrors([
                    'error' => "⚠️ You must watch at least 90% of the video to complete this task. You watched {$completionPercentage}%."
                ]);
            }
        }

        // Check 7: Product Review Minimum Length (for PRODUCT_REVIEW tasks)
        if ($task->taskTemplate->category === 'PRODUCT_REVIEW') {
            $reviewText = $validated['response_data']['review'] ?? '';
            $minLength = $settings->task_validation_rules['review_min_characters'] ?? 20;

            if (strlen(trim($reviewText)) < $minLength) {
                return back()->withErrors([
                    'error' => "⚠️ Review must be at least {$minLength} characters long. Please provide more detailed feedback."
                ]);
            }
        }

        // Check 8: Daily task completion limit
        $tasksCompletedToday = UserTask::where('user_id', $user->id)
            ->where('status', 'COMPLETED')
            ->whereDate('completed_at', today())
            ->count();

        // Get max daily tasks from user's plan features
        $maxDailyTasks = $user->features()->getMaxDailyTasks();

        if ($tasksCompletedToday >= $maxDailyTasks) {
            return back()->withErrors([
                'error' => "Daily task limit reached! You've completed {$tasksCompletedToday}/{$maxDailyTasks} tasks today. Upgrade your plan or refer friends to earn more!"
            ]);
        }

        DB::beginTransaction();
        try {
            // Mark task as completed
            $task->update([
                'status' => 'COMPLETED',
                'completed_at' => now(),
                'response_data' => $validated['response_data'],
                'completion_duration_seconds' => $validated['duration'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Credit user wallet - add to PENDING balance (72hr maturation)
            $wallet = auth()->user()->wallet;
            $wallet->increment('pending_balance', $task->reward_amount);
            $wallet->increment('total_earned', $task->reward_amount);

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'transaction_type' => 'TASK_EARNING',
                'balance_type' => 'PENDING',
                'amount' => $task->reward_amount,
                'status' => 'PENDING',
                'is_credit' => true,
                'description' => "Task completed: {$task->taskTemplate->title}",
                'metadata' => [
                    'task_id' => $task->id,
                    'task_category' => $task->taskTemplate->category,
                    'completion_time' => $validated['duration'],
                    'matures_at' => now()->addHours(72)->toDateTimeString() // 72 hour maturation
                ],
            ]);

            $task->update(['transaction_id' => $transaction->id, 'credited' => true]);

            // Update user stats
            $user = auth()->user();
            $user->update(['last_task_completed_at' => now()]);
            $user->increment('total_tasks_completed');
            $user->increment('tasks_completed_this_week');
            $user->increment('tasks_completed_this_month');

            // Increment task template completion count
            $task->taskTemplate->increment('current_completions');

            // Distribute commissions to upline (fan-out)
            $this->distributeCommissions($user, $task);

            DB::commit();

            return back()->with('success', "Task completed! ₦{$task->reward_amount} will be available after 72 hours.");

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error("Task completion failed: " . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to complete task. Please try again.']);
        }
    }

    /**
     * Distribute commissions to upline referrers (fan-out)
     * Commissions are credited DIRECTLY to withdrawable balance (skip maturation)
     */
    private function distributeCommissions($user, $task)
    {
        try {
            // Get settings
            $settings = GlobalSetting::first();
            $commissionRates = $settings->commission_rates['task_earnings'] ?? [];
            $maxDepth = $settings->referral_levels_depth ?? 5;

            if (empty($commissionRates)) {
                logger()->info("No commission rates configured, skipping distribution.");
                return;
            }

            // Get referral tree for this user
            $referralTree = ReferralTree::where('user_id', $user->id)->first();

            if (!$referralTree) {
                logger()->info("No referral tree found for user {$user->id}, skipping commission distribution.");
                return;
            }

            // Get all upline users with their levels
            $upline = $referralTree->getUplineForCommissions();

            if (empty($upline)) {
                logger()->info("User {$user->id} has no upline, skipping commission distribution.");
                return;
            }

            // Distribute commissions level by level
            foreach ($upline as $ancestor) {
                $level = $ancestor['level'];
                $uplineUserId = $ancestor['user_id'];

                // Skip if level exceeds configured depth or no rate defined
                if ($level > $maxDepth || !isset($commissionRates[$level])) {
                    continue;
                }

                $commissionRate = $commissionRates[$level];
                $commissionAmount = round(($task->reward_amount * $commissionRate) / 100, 2);

                if ($commissionAmount <= 0) {
                    continue;
                }

                // Record in commission ledger (PENDING - will be disbursed via batch job)
                CommissionLedger::create([
                    'user_id' => $uplineUserId,
                    'source_user_id' => $user->id,
                    'source_task_id' => $task->id,
                    'amount' => $commissionAmount,
                    'level' => $level,
                    'commission_rate' => $commissionRate,
                    'status' => 'PENDING', // Store in ledger, batch disburse later to withdrawable
                    'processed_at' => null, // Will be set when batch job processes
                ]);

                logger()->info("Commission recorded in ledger: ₦{$commissionAmount} for user {$uplineUserId} (Level {$level})");
            }

            logger()->info("Commission distribution completed for task {$task->id}");

        } catch (\Exception $e) {
            logger()->error("Commission distribution failed: {$e->getMessage()}", [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'exception' => $e->getTraceAsString()
            ]);
            // Don't throw - let task completion succeed even if commission fails
        }
    }
}
