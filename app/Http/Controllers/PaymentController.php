<?php

namespace App\Http\Controllers;

use App\Models\GlobalSetting;
use App\Models\Plan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function initiate(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:bank_transfer,crypto_transfer',
        ]);

        $user = auth()->user();
        $plan = Plan::findOrFail($request->plan_id);
        $settings = GlobalSetting::get();

        // Check if payment method is enabled
        $method = $request->payment_method;
        if (!($settings->payment_gateways[$method]['enabled'] ?? false)) {
            return back()->with('error', 'Selected payment method is currently unavailable.');
        }

        // Get payment accounts based on method
        $paymentAccounts = [];
        if ($method === 'bank_transfer') {
            $paymentAccounts = $settings->bank_accounts ?? [];
        } elseif ($method === 'crypto_transfer') {
            $paymentAccounts = $settings->crypto_wallets ?? [];
        }

        if (empty($paymentAccounts)) {
            return back()->with('error', 'No payment accounts available for this method.');
        }

        // Store plan and method in session for later
        session([
            'pending_payment' => [
                'plan_id' => $plan->id,
                'payment_method' => $method,
            ]
        ]);

        // Get crypto conversion if crypto payment
        $cryptoConversion = null;
        if ($method === 'crypto_transfer') {
            $cryptoConversion = [
                'usdtAmount' => \App\Helpers\CurrencyHelper::toUSDT($plan->price),
                'conversionDisplay' => \App\Helpers\CurrencyHelper::getConversionDisplay($plan->price),
            ];
        }

        // Redirect to payment page (transaction created after "I Have Paid")
        return Inertia::render('Payment/PaymentPage', [
            'plan' => $plan,
            'paymentMethod' => $method,
            'paymentAccounts' => $paymentAccounts,
            'currencySymbol' => $settings->currency_symbol ?? '₦',
            'cryptoConversion' => $cryptoConversion,
        ]);
    }

    public function viewPaymentDetails(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,transaction_hash',
        ]);

        $user = auth()->user();
        $settings = GlobalSetting::get();
        $transaction = Transaction::where('transaction_hash', $request->transaction_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // ✅ KEY FIX: Handle both ACTIVATION_PAYMENT and PLAN_UPGRADE transactions
        if ($transaction->transaction_type === 'PLAN_UPGRADE') {
            // For upgrade transactions, use new_plan_id or fall back to reference_id
            $planId = $transaction->metadata['plan_id'] ?? $transaction->metadata['new_plan_id'] ?? $transaction->reference_id;
            $plan = Plan::findOrFail($planId);

            // For upgrades, we need to show the discounted price
            $amount = $transaction->metadata['discounted_price'] ?? $transaction->amount;
            $isUpgrade = true;
            $discountPercentage = $transaction->metadata['discount_percentage'] ?? 0;
            $originalPrice = $transaction->metadata['original_price'] ?? $plan->price;
        } else {
            // For activation payments, use plan_id from metadata
            $plan = Plan::findOrFail($transaction->metadata['plan_id']);
            $amount = $plan->price;
            $isUpgrade = false;
            $discountPercentage = null;
            $originalPrice = null;
        }

        $method = $transaction->metadata['payment_method'];

        // Get payment accounts
        $paymentAccounts = [];
        if ($method === 'bank_transfer') {
            $paymentAccounts = $settings->bank_accounts ?? [];
        } elseif ($method === 'crypto_transfer') {
            $paymentAccounts = $settings->crypto_wallets ?? [];
        }

        // Get crypto conversion if crypto payment
        $cryptoConversion = null;
        if ($method === 'crypto_transfer') {
            // ✅ FIX: Use stored crypto data from transaction metadata if available
            if (isset($transaction->metadata['crypto_amount'])) {
                $cryptoConversion = [
                    'usdtAmount' => $transaction->metadata['crypto_amount'],
                    'conversionDisplay' => $transaction->metadata['conversion_display'] ?? \App\Helpers\CurrencyHelper::getConversionDisplay($amount),
                ];
            } else {
                // Fallback to calculating it
                $cryptoConversion = [
                    'usdtAmount' => \App\Helpers\CurrencyHelper::toUSDT($amount),
                    'conversionDisplay' => \App\Helpers\CurrencyHelper::getConversionDisplay($amount),
                ];
            }
        }

        return Inertia::render('Payment/PaymentPage', [
            'plan' => $plan,
            'paymentMethod' => $method,
            'paymentAccounts' => $paymentAccounts,
            'currencySymbol' => $settings->currency_symbol ?? '₦',
            'cryptoConversion' => $cryptoConversion,
            'existingTransaction' => true, // Flag to hide "I Have Paid" button
            'isUpgrade' => $isUpgrade,
            'discountPercentage' => $discountPercentage,
            'originalPrice' => $originalPrice,
            'discountedPrice' => $isUpgrade ? $amount : null,
        ]);
    }

    public function confirm(Request $request)
    {
        $user = auth()->user();
        $settings = GlobalSetting::get();
        $pendingPayment = session('pending_payment');

        if (!$pendingPayment) {
            return redirect()->route('dashboard')->with('error', 'No pending payment found.');
        }

        $plan = Plan::findOrFail($pendingPayment['plan_id']);

        // Generate transaction ID
        $transactionId = $this->generateTransactionId($user);

        // Prepare metadata
        $metadata = [
            'plan_id' => $plan->id,
            'plan_name' => $plan->display_name,
            'payment_method' => $pendingPayment['payment_method'],
            'confirmed_at' => now()->toDateTimeString(),
            'local_amount' => $plan->price,
            'local_currency' => $settings->currency ?? 'NGN',
        ];

        // Add crypto conversion if crypto payment
        if ($pendingPayment['payment_method'] === 'crypto_transfer') {
            $usdtAmount = \App\Helpers\CurrencyHelper::toUSDT($plan->price);
            $metadata['crypto_amount'] = $usdtAmount;
            $metadata['crypto_currency'] = 'USDT';
            $metadata['conversion_rate'] = \App\Helpers\CurrencyHelper::getUSDTRate();
            $metadata['conversion_display'] = \App\Helpers\CurrencyHelper::getConversionDisplay($plan->price);
        }

        // Create transaction record
        $transaction = Transaction::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'transaction_type' => 'ACTIVATION_PAYMENT',
            'balance_type' => 'PENDING',
            'status' => 'AWAITING_APPROVAL',
            'amount' => $plan->price,
            'currency' => $settings->currency ?? 'NGN',
            'is_credit' => true,
            'description' => "Activation payment for {$plan->display_name} plan",
            'reference_id' => $plan->id,
            'reference_type' => 'Plan',
            'metadata' => $metadata,
            'transaction_hash' => $transactionId,
        ]);

        // Clear session
        session()->forget('pending_payment');

        return Inertia::render('Payment/PendingApproval', [
            'transactionId' => $transactionId,
            'planName' => $plan->display_name,
            'createdAt' => $transaction->created_at->toIso8601String(),
            'supportEmail' => $settings->support_email ?? null,
            'supportPhone' => $settings->support_phone ?? null,
            'supportWhatsapp' => $settings->support_whatsapp ?? null,
        ]);
    }

    private function generateTransactionId($user): string
    {
        $country = strtoupper(substr($user->country ?? 'NGR', 0, 3));
        $random = strtoupper(Str::random(6));
        return "TXN-{$country}-{$random}";
    }
}
