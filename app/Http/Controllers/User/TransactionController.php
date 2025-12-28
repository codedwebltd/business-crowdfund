<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user()->load('wallet');

        // Filters
        $type = $request->input('type');
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Query transactions
        $query = Transaction::where('user_id', $user->id)
            ->with(['reference', 'processedBy'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($type) {
            $query->where('transaction_type', $type);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $transactions = $query->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total_earned' => Transaction::where('user_id', $user->id)
                ->where('is_credit', true)
                ->where('status', 'COMPLETED')
                ->sum('amount'),

            'total_withdrawn' => Transaction::where('user_id', $user->id)
                ->where('transaction_type', 'WITHDRAWAL')
                ->where('status', 'COMPLETED')
                ->sum('amount'),

            'pending_transactions' => Transaction::where('user_id', $user->id)
                ->where('status', 'PENDING')
                ->count(),

            'this_month_earnings' => Transaction::where('user_id', $user->id)
                ->where('is_credit', true)
                ->where('status', 'COMPLETED')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        // Transaction types for filter dropdown
        $transactionTypes = Transaction::where('user_id', $user->id)
            ->distinct()
            ->pluck('transaction_type');

        return Inertia::render('User/Transactions', [
            'transactions' => $transactions,
            'stats' => $stats,
            'transactionTypes' => $transactionTypes,
            'filters' => [
                'type' => $type,
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function show($id)
    {
        $transaction = Transaction::where('user_id', auth()->id())
            ->with(['reference', 'processedBy'])
            ->findOrFail($id);

        return response()->json($transaction);
    }
}
