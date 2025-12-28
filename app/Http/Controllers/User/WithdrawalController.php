<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Services\NotificationService;
use App\Services\PDFs\WithdrawalReceiptPDF;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Mail\Mailables\Attachment;

class WithdrawalController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = auth()->user()->load(['plan', 'rank', 'wallet', 'testimonials', 'latestKyc']);
        $settings = GlobalSetting::first();

        // Check if user has bank or crypto details
        $hasBankDetails = $user->bank_name && $user->account_number && $user->account_name;
        $hasCryptoDetails = $user->wallet_details &&
                           isset($user->wallet_details['wallet_address']) &&
                           isset($user->wallet_details['coin_name']) &&
                           isset($user->wallet_details['coin_network']);

        // Get withdrawal limits based on plan and rank
        $planLimits = $settings->withdrawal_limits_by_plan[$user->plan_id] ?? [
            'min' => 1000,
            'max' => 10000,
            'per_day' => 1,
        ];

        $rankLimits = $user->rank ? ($settings->withdrawal_limits_by_rank[$user->rank_id] ?? null) : null;

        // Use rank limits if better, otherwise use plan limits
        $limits = [
            'min' => $rankLimits['min'] ?? $planLimits['min'],
            'max' => $rankLimits['max'] ?? $planLimits['max'],
            'per_day' => $rankLimits['per_day'] ?? $planLimits['per_day'],
        ];

        // Check if KYC is required
        $kycRequired = false;
        $kycThreshold = $settings->kyc_requirements['withdrawal_requirements']['nin']['threshold'] ?? 50000;
        if ($user->wallet && $user->wallet->withdrawable_balance >= $kycThreshold) {
            $kycRequired = !$user->latestKyc || $user->latestKyc->status !== 'APPROVED';
        }

        // Check if testimonial is required (first withdrawal)
        $testimonialRequired = $settings->require_testimonial_first_withdrawal &&
                              $user->testimonials()->where('status', 'APPROVED')->count() === 0;

        // Get today's withdrawal count
        $todayWithdrawals = $user->withdrawals()->whereDate('created_at', today())->count();
        $canWithdrawToday = $todayWithdrawals < $limits['per_day'];

        // Token pricing
        $tokenPrice = $settings->token_settings['token_price'] ?? 850;
        $withdrawalRate = $settings->withdrawal_rate ?? 1.0;

        return Inertia::render('User/Withdrawal', [
            'user' => $user,
            'settings' => $settings,
            'limits' => $limits,
            'hasBankDetails' => $hasBankDetails,
            'hasCryptoDetails' => $hasCryptoDetails,
            'kycRequired' => $kycRequired,
            'testimonialRequired' => $testimonialRequired,
            'canWithdrawToday' => $canWithdrawToday,
            'todayWithdrawals' => $todayWithdrawals,
            'tokenPrice' => $tokenPrice,
            'withdrawalRate' => $withdrawalRate,
            'bankEnabled' => $settings->payment_gateways['bank_transfer']['enabled'] ?? false,
            'cryptoEnabled' => $settings->payment_gateways['crypto_transfer']['enabled'] ?? true,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user()->load(['rank', 'wallet']);
        $settings = GlobalSetting::first();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'method' => 'required|in:bank,crypto',
        ]);

        $originalAmount = $validated['amount'];
        $withdrawalRate = $settings->withdrawal_rate ?? 1.0;
        $finalAmount = $originalAmount * $withdrawalRate;

        // Validate withdrawal using existing model method
        $validation = Withdrawal::canUserWithdraw($user, $originalAmount);

        if (!$validation['can_withdraw']) {
            return back()->withErrors(['amount' => $validation['errors'][0]]);
        }

        // Deduct from wallet immediately
        $user->wallet->decrement('withdrawable_balance', $finalAmount);

        // Create withdrawal record
        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount_requested' => $finalAmount, // Store final amount after rate
            'meta_data' => [
                'original_amount' => $originalAmount,
                'withdrawal_rate' => $withdrawalRate,
                'token_price' => $settings->token_settings['token_price'] ?? 850,
                'tokens_deducted' => $originalAmount / ($settings->token_settings['token_price'] ?? 850),
            ],
            'payment_method' => strtoupper($validated['method']),
            'bank_name' => $validated['method'] === 'bank' ? $user->bank_name : null,
            'account_number' => $validated['method'] === 'bank' ? $user->account_number : null,
            'account_name' => $validated['method'] === 'bank' ? $user->account_name : null,
            'wallet_details' => $validated['method'] === 'crypto' ? $user->wallet_details : null,
            'status' => 'PENDING',
            'requested_at' => now(),
            'priority_score' => $user->calculatePriorityScore(),
            'is_first_withdrawal' => $validation['is_first_withdrawal'],
        ]);

        // Create transaction record for tracking
        Transaction::create([
            'user_id' => $user->id,
            'transaction_type' => 'WITHDRAWAL',
            'balance_type' => 'WITHDRAWABLE',
            'status' => 'PENDING',
            'amount' => $finalAmount,
            'currency' => 'NGN',
            'is_credit' => false, // Debit transaction
            'description' => "Withdrawal request via " . strtoupper($validated['method']) . " - ₦" . number_format($finalAmount, 2),
            'reference_id' => $withdrawal->id,
            'reference_type' => 'App\Models\Withdrawal',
            'metadata' => [
                'payment_method' => $validated['method'],
                'original_amount' => $originalAmount,
                'withdrawal_rate' => $withdrawalRate,
                'is_first_withdrawal' => $validation['is_first_withdrawal'],
            ],
        ]);

        // Generate PDF receipt
        $pdfGenerator = new WithdrawalReceiptPDF($withdrawal);
        $pdfContent = $pdfGenerator->output();

        // Save PDF temporarily for email attachment
        $pdfPath = storage_path('app/temp/withdrawal-receipt-' . $withdrawal->id . '.pdf');
        if (!file_exists(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0755, true);
        }
        file_put_contents($pdfPath, $pdfContent);

        // Create attachment array for email
        $attachments = [
            Attachment::fromPath($pdfPath)
                ->as('Withdrawal-Receipt-' . ($withdrawal->transaction_reference ?? $withdrawal->id) . '.pdf')
                ->withMime('application/pdf')
        ];

        // Send multi-channel notifications with PDF attachment
        $this->notificationService->send($user, 'withdrawal_requested', [
            'amount' => $finalAmount,
            'payment_method' => $validated['method'],
            'rank' => $user->rank->name ?? 'bronze',
            'withdrawal_id' => $withdrawal->id,
            'attachments' => $attachments,
        ]);

        // Notify admin
        if ($settings->support_email) {
            try {
                \Mail::to($settings->support_email)->send(
                    new \App\Mail\GenericNotification(
                        $user,
                        "New Withdrawal Request - ₦" . number_format($finalAmount, 2),
                        "User {$user->full_name} (ID: {$user->id}) has requested a withdrawal of ₦" . number_format($finalAmount, 2) . " via {$validated['method']}.\n\nWithdrawal ID: {$withdrawal->id}\nPriority Score: {$withdrawal->priority_score}\nFirst Withdrawal: " . ($withdrawal->is_first_withdrawal ? 'Yes' : 'No'),
                        $attachments
                    )
                );
            } catch (\Exception $e) {
                logger()->error("Failed to notify admin of withdrawal: " . $e->getMessage());
            }
        }

        // Clean up temporary PDF file after a delay (optional)
        // You can also set up a scheduled task to clean temp files regularly

        return back()->with('success', 'Withdrawal request submitted successfully!');
    }
}
