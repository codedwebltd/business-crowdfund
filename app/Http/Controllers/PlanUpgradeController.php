<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Transaction;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PlanUpgradeController extends Controller
{
    public function showPayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:bank_transfer,crypto_transfer',
            'discount_percentage' => 'required|numeric',
            'discounted_price' => 'required|numeric',
        ]);

        $user = auth()->user();
        $plan = Plan::findOrFail($request->plan_id);
        $settings = GlobalSetting::first();

        // Check if user already has pending upgrade transaction
        $existingTransaction = Transaction::where('user_id', $user->id)
            ->where('transaction_type', 'PLAN_UPGRADE')
            ->where('status', 'AWAITING_APPROVAL')
            ->exists();

        // ✅ KEY FIX: Store payment data in session for confirmPayment (matching registration flow)
        session([
            'pending_upgrade' => [
                'plan_id' => $plan->id,
                'payment_method' => $request->payment_method,
                'discount_percentage' => $request->discount_percentage,
                'discounted_price' => $request->discounted_price,
            ]
        ]);

        // Get payment accounts based on method
        $paymentAccounts = [];
        $cryptoConversion = null;

        if ($request->payment_method === 'bank_transfer') {
            $paymentAccounts = $settings->bank_accounts ?? [];
        } else {
            $paymentAccounts = $settings->crypto_wallets ?? [];

            // ✅ FIX: Use CurrencyHelper for proper crypto conversion (matching registration flow)
            $cryptoConversion = [
                'usdtAmount' => \App\Helpers\CurrencyHelper::toUSDT($request->discounted_price),
                'conversionDisplay' => \App\Helpers\CurrencyHelper::getConversionDisplay($request->discounted_price),
            ];
        }

        return Inertia::render('Payment/PaymentPage', [
            'plan' => $plan,
            'paymentMethod' => $request->payment_method,
            'paymentAccounts' => $paymentAccounts,
            'currencySymbol' => $settings->currency_symbol ?? '₦',
            'cryptoConversion' => $cryptoConversion,
            'existingTransaction' => $existingTransaction,
            'isUpgrade' => true,
            'discountPercentage' => $request->discount_percentage,
            'originalPrice' => $plan->price,
            'discountedPrice' => $request->discounted_price,
        ]);
    }

    public function confirmPayment(Request $request)
    {
        $user = auth()->user();
        $settings = GlobalSetting::first();

        // Check if user already has pending upgrade transaction
        $existingTransaction = Transaction::where('user_id', $user->id)
            ->where('transaction_type', 'PLAN_UPGRADE')
            ->where('status', 'AWAITING_APPROVAL')
            ->first();

        if ($existingTransaction) {
            return redirect()->route('dashboard')->with('info', 'You already have a pending upgrade request.');
        }

        // Get payment data from session (stored by showPayment)
        $pendingUpgrade = session('pending_upgrade');

        if (!$pendingUpgrade) {
            return redirect()->route('dashboard')->with('error', 'No pending upgrade found. Please try again.');
        }

        // Get upgrade offer from session or recalculate
        $starRating = $user->performance->star_rating ?? 1;
        $qualifiedPlan = Plan::findOrFail($pendingUpgrade['plan_id']);

        if (!$qualifiedPlan || $qualifiedPlan->order <= $user->plan->order) {
            return redirect()->route('dashboard')->with('error', 'You do not qualify for an upgrade at this time.');
        }

        $discount = $settings->plan_upgrade_discount_percentage ?? 20;
        $discountedPrice = $qualifiedPlan->price * (1 - $discount / 100);

        // Prepare metadata
        $metadata = [
            'plan_id' => $qualifiedPlan->id,  // ✅ KEY FIX: Add plan_id for viewPaymentDetails
            'plan_name' => $qualifiedPlan->display_name,
            'current_plan_id' => $user->plan_id,
            'current_plan_name' => $user->plan->display_name,
            'new_plan_id' => $qualifiedPlan->id,
            'new_plan_name' => $qualifiedPlan->display_name,
            'original_price' => $qualifiedPlan->price,
            'discount_percentage' => $discount,
            'discounted_price' => $discountedPrice,
            'star_rating' => $starRating,
            'payment_method' => $pendingUpgrade['payment_method'],  // ✅ KEY FIX: Store payment method
            'confirmed_at' => now()->toDateTimeString(),
            'local_amount' => $discountedPrice,
            'local_currency' => $settings->currency ?? 'NGN',
        ];

        // ✅ KEY FIX: Add crypto conversion data if crypto payment
        if ($pendingUpgrade['payment_method'] === 'crypto_transfer') {
            $usdtAmount = \App\Helpers\CurrencyHelper::toUSDT($discountedPrice);
            $metadata['crypto_amount'] = $usdtAmount;
            $metadata['crypto_currency'] = 'USDT';
            $metadata['conversion_rate'] = \App\Helpers\CurrencyHelper::getUSDTRate();
            $metadata['conversion_display'] = \App\Helpers\CurrencyHelper::getConversionDisplay($discountedPrice);
        }

        // Create transaction
        $transaction = Transaction::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'transaction_type' => 'PLAN_UPGRADE',
            'balance_type' => 'PENDING',
            'status' => 'AWAITING_APPROVAL',
            'amount' => $discountedPrice,
            'currency' => $settings->currency ?? 'NGN',
            'is_credit' => true,
            'description' => "Plan upgrade from {$user->plan->display_name} to {$qualifiedPlan->display_name}",
            'reference_id' => $qualifiedPlan->id,
            'reference_type' => 'Plan',
            'transaction_hash' => 'UPG-' . strtoupper(uniqid()),
            'metadata' => $metadata,
        ]);

        // Clear session
        session()->forget('pending_upgrade');

        // Use Inertia location for full page reload (prevents browser crash)
        return Inertia::location(route('dashboard'));
    }
}
