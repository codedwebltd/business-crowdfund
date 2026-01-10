<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Services\NotificationService;
use App\Services\PDFs\WithdrawalReceiptPDF;
use App\Helpers\CurrencyHelper;
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

        // Get global withdrawal limits (baseline/override)
        $globalMin = $settings->minimum_withdrawal ?? 1000;
        $globalMax = $settings->maximum_withdrawal ?? 500000;
        $globalPerDay = $settings->withdrawals_per_day ?? 1;

        // Get rank-specific limits (if user has rank)
        $rankLimits = $user->rank ? ($settings->withdrawal_limits_by_rank[$user->rank_id] ?? null) : null;

        // Apply stricter-wins logic: Global overrides when stricter
        // - For minimum: Higher value is stricter (use MAX)
        // - For maximum: Lower value is stricter (use MIN)
        // - For per_day: Lower value is stricter (use MIN)
        $limits = [
            'min' => $rankLimits ? max($globalMin, $rankLimits['min']) : $globalMin,
            'max' => $rankLimits ? min($globalMax, $rankLimits['max']) : $globalMax,
            'per_day' => $rankLimits ? min($globalPerDay, $rankLimits['per_day'] ?? $globalPerDay) : $globalPerDay,
        ];

        // Check if KYC is required
        $kycRequired = false;
        $kycThreshold = $settings->kyc_withdrawal_threshold ?? 50000;
        $enableKycOnFirstWithdrawal = $settings->enable_kyc_on_first_withdrawal ?? false;
        $isFirstWithdrawal = $user->withdrawals()->where('status', '!=', 'FAILED')->count() === 0;

        // Require KYC if: (1) First withdrawal and setting enabled, OR (2) Balance >= threshold
        if ($enableKycOnFirstWithdrawal && $isFirstWithdrawal) {
            $kycRequired = !$user->latestKyc || $user->latestKyc->status !== 'APPROVED';
        } elseif ($user->wallet && $user->wallet->withdrawable_balance >= $kycThreshold) {
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

        // Check referral threshold (active referrals with rank_id and status = ACTIVE)
        $referralThreshold = $settings->referral_threshold ?? 0;
        $activeReferralsCount = $user->referrals()
            ->whereNotNull('rank_id')
            ->where('status', 'ACTIVE')
            ->count();
        $referralThresholdMet = $referralThreshold == 0 || $activeReferralsCount >= $referralThreshold;

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
            'twoFactorEnabled' => $user->google2fa_enabled ?? false,
            'referralThreshold' => $referralThreshold,
            'activeReferralsCount' => $activeReferralsCount,
            'referralThresholdMet' => $referralThresholdMet,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user()->load(['rank', 'wallet', 'latestKyc']);
        $settings = GlobalSetting::first();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'method' => 'required|in:bank,crypto',
            'two_factor_code' => $user->google2fa_enabled ? 'required|digits:6' : '',
        ]);

        // Verify 2FA if enabled
        if ($user->google2fa_enabled) {
            $google2fa = app(\App\Services\Google2FAService::class);
            if (!$google2fa->verify($user->google2fa_secret, $validated['two_factor_code'])) {
                return back()->withErrors(['two_factor_code' => 'Invalid 2FA code. Please try again.']);
            }
        }

        // Check KYC requirement (server-side enforcement)
        $kycThreshold = $settings->kyc_withdrawal_threshold ?? 50000;
        $enableKycOnFirstWithdrawal = $settings->enable_kyc_on_first_withdrawal ?? false;
        $isFirstWithdrawal = $user->withdrawals()->where('status', '!=', 'FAILED')->count() === 0;

        $kycRequired = false;
        if ($enableKycOnFirstWithdrawal && $isFirstWithdrawal) {
            $kycRequired = true;
        } elseif ($user->wallet && $user->wallet->withdrawable_balance >= $kycThreshold) {
            $kycRequired = true;
        }

        if ($kycRequired && (!$user->latestKyc || $user->latestKyc->status !== 'APPROVED')) {
            return back()->withErrors(['amount' => 'KYC verification is required before you can proceed with this withdrawal. Please complete your KYC verification.']);
        }

        // Check referral threshold
        $referralThreshold = $settings->referral_threshold ?? 0;
        if ($referralThreshold > 0) {
            $activeReferralsCount = $user->referrals()
                ->whereNotNull('rank_id')
                ->where('status', 'ACTIVE')
                ->count();

            if ($activeReferralsCount < $referralThreshold) {
                return back()->withErrors(['amount' => "You need {$referralThreshold} active referrals to withdraw. You currently have {$activeReferralsCount}. Invite more friends to unlock withdrawals!"]);
            }
        }

        $originalAmount = $validated['amount'];
        $withdrawalRate = $settings->withdrawal_rate ?? 1.0;
        $finalAmount = $originalAmount * $withdrawalRate;

        // Calculate crypto value in USDT (no cache for real-time rate)
        $cryptoValueUSDT = CurrencyHelper::toUSDT($finalAmount, null, true);
        $usdtRate = CurrencyHelper::getUSDTRate(null, true);

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
                'crypto_value_usdt' => $cryptoValueUSDT,
                'usdt_rate' => $usdtRate,
                'crypto_calculated_at' => now()->toDateTimeString(),
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
        $currencySymbol = $settings->currency_symbol ?? '₦';
        Transaction::create([
            'user_id' => $user->id,
            'transaction_type' => 'WITHDRAWAL',
            'balance_type' => 'WITHDRAWABLE',
            'status' => 'PENDING',
            'priority' => $user->getPriorityLevel(), // Set priority from user performance (1-5)
            'amount' => $finalAmount,
            'currency' => 'NGN',
            'is_credit' => false, // Debit transaction
            'description' => "Withdrawal request via " . strtoupper($validated['method']) . " - {$currencySymbol}" . number_format($finalAmount, 2),
            'reference_id' => $withdrawal->id,
            'reference_type' => 'App\Models\Withdrawal',
            'metadata' => [
                'payment_method' => $validated['method'],
                'original_amount' => $originalAmount,
                'withdrawal_rate' => $withdrawalRate,
                'is_first_withdrawal' => $validation['is_first_withdrawal'],
                'crypto_value_usdt' => $cryptoValueUSDT,
                'usdt_rate' => $usdtRate,
                'crypto_calculated_at' => now()->toDateTimeString(),
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
                        "New Withdrawal Request - {$currencySymbol}" . number_format($finalAmount, 2),
                        "User {$user->full_name} (ID: {$user->id}) has requested a withdrawal of {$currencySymbol}" . number_format($finalAmount, 2) . " via {$validated['method']}.\n\nWithdrawal ID: {$withdrawal->id}\nPriority Score: {$withdrawal->priority_score}\nFirst Withdrawal: " . ($withdrawal->is_first_withdrawal ? 'Yes' : 'No'),
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
