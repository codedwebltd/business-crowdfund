<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\GlobalSetting;
use App\Services\NotificationService;
use App\Services\PDFs\WelcomeContractPDF;
use App\Services\PDFs\TermsAndConditionsPDF;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Attachment;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index()
    {
        // Fetch ALL transactions with user and performance relationships
        $transactions = Transaction::with(['user', 'user.performance'])
            ->latest()
            ->get();

        // Stats for activation payments (for backwards compatibility)
        $stats = [
            'pending' => Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')
                ->where('status', 'AWAITING_APPROVAL')->count(),
            'approved' => Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')
                ->where('status', 'APPROVED')->count(),
            'rejected' => Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')
                ->where('status', 'REJECTED')->count(),
        ];

        // Transaction type statistics
        $typeStats = [
            'ACTIVATION_PAYMENT' => Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')->count(),
            'TASK_EARNING' => Transaction::where('transaction_type', 'TASK_EARNING')->count(),
            'REFERRAL_BONUS' => Transaction::where('transaction_type', 'REFERRAL_BONUS')->count(),
            'TEAM_COMMISSION' => Transaction::where('transaction_type', 'TEAM_COMMISSION')->count(),
            'WITHDRAWAL' => Transaction::where('transaction_type', 'WITHDRAWAL')->count(),
        ];

        $settings = GlobalSetting::get();

        return Inertia::render('Admin/Transactions', [
            'transactions' => $transactions,
            'stats' => $stats,
            'typeStats' => $typeStats,
            'currencySymbol' => $settings->currency_symbol ?? '₦',
            'settings' => $settings,
        ]);
    }

    public function approve(Request $request, $id)
    {
        $transaction = Transaction::with('user')->findOrFail($id);

        if ($transaction->status !== 'AWAITING_APPROVAL') {
            return back()->with('error', 'Transaction already processed.');
        }

        // Update transaction
        $transaction->update([
            'status' => 'APPROVED',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        $user = $transaction->user;

        // Handle different transaction types
        if ($transaction->transaction_type === 'PLAN_UPGRADE') {
            return $this->approvePlanUpgrade($transaction, $user);
        }

        // Original activation payment flow
        $plan = \App\Models\Plan::find($transaction->metadata['plan_id']);

        // Activate user
        $user->update([
            'status' => 'ACTIVE',
            'plan_id' => $transaction->metadata['plan_id'],
            'activation_amount' => $transaction->amount,
            'activation_date' => now(),
        ]);

        // Assign rank based on purchased plan
        if ($plan) {
            $user->assignRankFromPlan($plan);
        }

        // Generate TOS and Partnership PDFs for first activation
        $attachments = [];

        // Generate Terms and Conditions PDF
        $tosPdfGenerator = new TermsAndConditionsPDF($user);
        $tosPdfContent = $tosPdfGenerator->output();
        $tosPdfPath = storage_path('app/temp/tos-' . $user->id . '.pdf');
        if (!file_exists(dirname($tosPdfPath))) {
            mkdir(dirname($tosPdfPath), 0755, true);
        }
        file_put_contents($tosPdfPath, $tosPdfContent);

        $attachments[] = Attachment::fromPath($tosPdfPath)
            ->as('Terms-and-Conditions.pdf')
            ->withMime('application/pdf');

        // Generate Partnership Agreement PDF
        $partnershipPdfGenerator = new WelcomeContractPDF($user);
        $partnershipPdfContent = $partnershipPdfGenerator->output();
        $partnershipPdfPath = storage_path('app/temp/partnership-' . $user->id . '.pdf');
        file_put_contents($partnershipPdfPath, $partnershipPdfContent);

        $attachments[] = Attachment::fromPath($partnershipPdfPath)
            ->as('Partnership-Agreement.pdf')
            ->withMime('application/pdf');

        // Send notification with PDFs
        app(NotificationService::class)->send($user, 'account_activated', [
            'plan_name' => $transaction->metadata['plan_name'],
            'amount' => $transaction->amount,
            'attachments' => $attachments,
        ]);

        // Credit direct referrer with referral bonus
        if ($user->referred_by_id) {
            $this->creditReferralBonus($user, $transaction);
        }

        return back()->with('success', 'Payment approved! User activated with TOS & Partnership PDFs sent.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        $transaction = Transaction::with('user')->findOrFail($id);

        if ($transaction->status !== 'AWAITING_APPROVAL') {
            return back()->with('error', 'Transaction already processed.');
        }

        $transaction->update([
            'status' => 'REJECTED',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'metadata' => array_merge($transaction->metadata ?? [], [
                'rejection_reason' => $request->reason,
            ]),
        ]);

        // Notify user
        app(NotificationService::class)->send($transaction->user, 'payment_rejected', [
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Payment rejected.');
    }

    /**
     * Credit upline referrers with activation bonus (multi-level)
     * Per blueprint: Level 1=20%, L2=10%, L3=5%, L4=3%, L5=2%, etc.
     */
    private function creditReferralBonus($user, $activationTransaction)
    {
        try {
            // Get referral tree for this user
            $referralTree = \App\Models\ReferralTree::where('user_id', $user->id)->first();

            if (!$referralTree) {
                \Log::info("No referral tree found for user {$user->id}, skipping activation bonus.");
                return;
            }

            // Get all upline users
            $upline = $referralTree->getUplineForCommissions();

            if (empty($upline)) {
                \Log::info("User {$user->id} has no upline, skipping activation bonus.");
                return;
            }

            // Get settings
            $settings = GlobalSetting::first();
            $activationRates = $settings->commission_rates['activation'] ?? [];
            $maxDepth = $settings->referral_levels_depth ?? 5;

            // Default rates per blueprint if not configured
            $defaultRates = [
                1 => 20, 2 => 10, 3 => 5, 4 => 3, 5 => 2,
                6 => 1, 7 => 1, 8 => 1, 9 => 1, 10 => 1,
                11 => 0.5, 12 => 0.5, 13 => 0.5, 14 => 0.5, 15 => 0.5,
                16 => 0.5, 17 => 0.5, 18 => 0.5, 19 => 0.5, 20 => 0.5,
            ];

            // Distribute to each level
            foreach ($upline as $ancestor) {
                $level = $ancestor['level'];
                $uplineUserId = $ancestor['user_id'];

                // Skip if level exceeds configured depth
                if ($level > $maxDepth) {
                    continue;
                }

                // Get commission rate for this level
                $bonusRate = $activationRates[$level] ?? $defaultRates[$level] ?? 0;

                if ($bonusRate <= 0) {
                    continue;
                }

                $bonusAmount = round(($activationTransaction->amount * $bonusRate) / 100, 2);

                if ($bonusAmount <= 0) {
                    continue;
                }

                // Get upline user
                $uplineUser = User::find($uplineUserId);

                if (!$uplineUser) {
                    continue;
                }

                // Credit upline user's wallet
                $uplineWallet = $uplineUser->wallet;
                $uplineWallet->increment('withdrawable_balance', $bonusAmount);
                $uplineWallet->increment('total_earned', $bonusAmount);

                // Create referral bonus transaction
                Transaction::create([
                    'user_id' => $uplineUserId,
                    'transaction_type' => 'REFERRAL_BONUS',
                    'balance_type' => 'WITHDRAWABLE',
                    'amount' => $bonusAmount,
                    'status' => 'COMPLETED',
                    'is_credit' => true,
                    'description' => "Level {$level} referral bonus for {$user->full_name}'s activation",
                    'reference_id' => $activationTransaction->id,
                    'reference_type' => 'Transaction',
                    'metadata' => [
                        'referred_user_id' => $user->id,
                        'referred_user_name' => $user->full_name,
                        'activation_amount' => $activationTransaction->amount,
                        'bonus_rate' => $bonusRate,
                        'level' => $level,
                    ],
                ]);

                // Send notification to upline user
                app(NotificationService::class)->send($uplineUser, 'referral_bonus', [
                    'amount' => $bonusAmount,
                    'referred_user_name' => $user->full_name,
                    'level' => $level,
                    'message' => "Congratulations! You earned ₦" . number_format($bonusAmount, 2) . " Level {$level} referral bonus for {$user->full_name}'s activation.",
                ]);

                \Log::info("Activation referral bonus credited", [
                    'upline_user_id' => $uplineUserId,
                    'level' => $level,
                    'amount' => $bonusAmount,
                    'referred_user_id' => $user->id,
                ]);
            }

        } catch (\Exception $e) {
            \Log::error("Failed to credit referral bonus: " . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    protected function approvePlanUpgrade(Transaction $transaction, $user)
    {
        $newPlanId = $transaction->metadata['new_plan_id'];
        $newPlan = \App\Models\Plan::find($newPlanId);

        if (!$newPlan) {
            return back()->with('error', 'New plan not found.');
        }

        // Update user's plan
        $user->update([
            'plan_id' => $newPlanId,
        ]);

        // Update rank if needed
        $user->assignRankFromPlan($newPlan);

        // Send notification
        app(NotificationService::class)->send($user, 'account_activated', [
            'plan_name' => $transaction->metadata['new_plan_name'],
            'amount' => $transaction->amount,
        ]);

        return back()->with('success', 'Plan upgrade approved! User upgraded to ' . $newPlan->display_name);
    }
}
