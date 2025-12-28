<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\GlobalSetting;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('user')
            ->where('transaction_type', 'ACTIVATION_PAYMENT')
            ->whereIn('status', ['AWAITING_APPROVAL', 'APPROVED', 'REJECTED'])
            ->latest()
            ->get();

        $stats = [
            'pending' => Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')
                ->where('status', 'AWAITING_APPROVAL')->count(),
            'approved' => Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')
                ->where('status', 'APPROVED')->count(),
            'rejected' => Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')
                ->where('status', 'REJECTED')->count(),
        ];

        $settings = GlobalSetting::get();

        return Inertia::render('Admin/Transactions', [
            'transactions' => $transactions,
            'stats' => $stats,
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

        // Activate user
        $user->update([
            'status' => 'ACTIVE',
            'plan_id' => $transaction->metadata['plan_id'],
            'activation_amount' => $transaction->amount,
            'activation_date' => now(),
        ]);

        // Send notification
        app(NotificationService::class)->send($user, 'account_activated', [
            'plan_name' => $transaction->metadata['plan_name'],
            'amount' => $transaction->amount,
        ]);

        return back()->with('success', 'Payment approved! User activated.');
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
}
