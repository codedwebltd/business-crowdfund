<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\User;
use App\Models\GlobalSetting;
use App\Services\NotificationService;
use App\Services\PDFs\WithdrawalReceiptPDF;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Attachment;
use Inertia\Inertia;

class WithdrawalController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $withdrawals = Withdrawal::with(['user', 'user.performance'])
            ->whereIn('status', ['PENDING', 'PROCESSING', 'COMPLETED', 'REJECTED'])
            ->orderBy('priority_score', 'desc')
            ->latest()
            ->get();

        $stats = [
            'pending' => Withdrawal::where('status', 'PENDING')->count(),
            'processing' => Withdrawal::where('status', 'PROCESSING')->count(),
            'completed' => Withdrawal::where('status', 'COMPLETED')->count(),
            'rejected' => Withdrawal::where('status', 'REJECTED')->count(),
        ];

        $settings = GlobalSetting::first();

        return Inertia::render('Admin/Withdrawals', [
            'withdrawals' => $withdrawals,
            'stats' => $stats,
            'currencySymbol' => $settings->currency_symbol ?? '₦',
            'settings' => $settings,
        ]);
    }

    public function approve(Request $request, $id)
    {
        $withdrawal = Withdrawal::with('user')->findOrFail($id);

        if (!in_array($withdrawal->status, ['PENDING', 'PROCESSING'])) {
            return back()->with('error', 'Withdrawal already processed.');
        }

        // Update withdrawal status
        $withdrawal->update([
            'status' => 'COMPLETED',
            'processed_at' => now(),
            'approved_by_id' => auth()->id(),
        ]);

        // Update associated transaction
        if ($withdrawal->transaction) {
            $withdrawal->transaction->update(['status' => 'COMPLETED']);
        }

        // Generate NEW PDF receipt with COMPLETED status
        $pdfGenerator = new WithdrawalReceiptPDF($withdrawal);
        $pdfContent = $pdfGenerator->output();

        // Save PDF temporarily for email attachment
        $pdfPath = storage_path('app/temp/withdrawal-receipt-completed-' . $withdrawal->id . '.pdf');
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

        // Send notification WITH receipt attachment
        $this->notificationService->send($withdrawal->user, 'withdrawal_completed', [
            'amount' => $withdrawal->amount_requested,
            'payment_method' => $withdrawal->payment_method,
            'withdrawal_id' => $withdrawal->id,
            'attachments' => $attachments,
        ]);

        return back()->with('success', 'Withdrawal approved! User notified with receipt.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        $withdrawal = Withdrawal::with('user')->findOrFail($id);

        if (!in_array($withdrawal->status, ['PENDING', 'PROCESSING'])) {
            return back()->with('error', 'Withdrawal already processed.');
        }

        // Refund user's wallet
        $user = $withdrawal->user;
        $user->wallet->increment('withdrawable_balance', $withdrawal->amount_requested);

        // Update withdrawal
        $withdrawal->update([
            'status' => 'REJECTED',
            'rejected_at' => now(),
            'approved_by_id' => auth()->id(),
            'rejection_reason' => $request->reason,
        ]);

        // Update associated transaction
        if ($withdrawal->transaction) {
            $withdrawal->transaction->update([
                'status' => 'FAILED',
                'metadata' => array_merge($withdrawal->transaction->metadata ?? [], [
                    'rejection_reason' => $request->reason,
                ]),
            ]);
        }

        // Notify user
        $this->notificationService->send($withdrawal->user, 'withdrawal_rejected', [
            'amount' => $withdrawal->amount_requested,
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Withdrawal rejected. Balance restored.');
    }

    public function markProcessing(Request $request, $id)
    {
        $withdrawal = Withdrawal::with('user')->findOrFail($id);

        if ($withdrawal->status !== 'PENDING') {
            return back()->with('error', 'Can only mark pending withdrawals as processing.');
        }

        $withdrawal->update([
            'status' => 'PROCESSING',
            'approved_by_id' => auth()->id(),
        ]);

        // Optional: Notify user (brief update)
        $this->notificationService->send($withdrawal->user, 'withdrawal_processing', [
            'amount' => $withdrawal->amount_requested,
            'withdrawal_id' => $withdrawal->id,
        ]);

        return back()->with('success', 'Withdrawal marked as processing. User notified.');
    }
}
