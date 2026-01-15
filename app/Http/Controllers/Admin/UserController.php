<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Rank;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['plan', 'rank', 'wallet', 'performance', 'referrer'])
            ->withCount(['directReferrals', 'transactions', 'withdrawals', 'tasks']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone_number', 'LIKE', "%{$search}%")
                    ->orWhere('referral_code', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('rank_id')) {
            $query->where('rank_id', $request->rank_id);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        if ($request->filled('kyc_status')) {
            if ($request->kyc_status === 'verified') {
                $query->whereNotNull('kyc_verified_at');
            } else {
                $query->whereNull('kyc_verified_at');
            }
        }

        if ($request->filled('task_ban')) {
            if ($request->task_ban === 'banned') {
                $query->whereNotNull('task_ban_until')->where('task_ban_until', '>', now());
            } else {
                $query->where(function ($q) {
                    $q->whereNull('task_ban_until')->orWhere('task_ban_until', '<=', now());
                });
            }
        }

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $users = $query->paginate(20)->withQueryString();

        // Get stats
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'ACTIVE')->count(),
            'pending' => User::where('status', 'PENDING')->count(),
            'suspended' => User::where('status', 'SUSPENDED')->count(),
            'banned' => User::where('status', 'BANNED')->count(),
            'kyc_verified' => User::whereNotNull('kyc_verified_at')->count(),
            'task_banned' => User::whereNotNull('task_ban_until')->where('task_ban_until', '>', now())->count(),
            'agents' => User::where('user_type', 'AGENT')->count(),
        ];

        // Get filter options
        $plans = Plan::select('id', 'name', 'display_name')->get();
        $ranks = Rank::select('id', 'name')->where('is_active', true)->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'stats' => $stats,
            'plans' => $plans,
            'ranks' => $ranks,
            'filters' => $request->only(['search', 'status', 'user_type', 'rank_id', 'plan_id', 'kyc_status', 'task_ban']),
        ]);
    }

    public function show(User $user)
    {
        $user->load([
            'plan',
            'rank',
            'wallet',
            'performance',
            'referrer',
            'latestKyc',
            'directReferrals' => function ($query) {
                $query->with(['plan', 'wallet'])->limit(10);
            },
        ]);

        $user->loadCount([
            'directReferrals',
            'transactions',
            'withdrawals',
            'tasks',
        ]);

        // Get recent activities
        $recentTransactions = $user->transactions()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentWithdrawals = $user->withdrawals()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentTasks = $user->tasks()
            ->with('taskTemplate')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Stats
        $stats = [
            'total_earned' => $user->wallet ? $user->wallet->total_earned : 0,
            'withdrawable_balance' => $user->wallet ? $user->wallet->withdrawable_balance : 0,
            'pending_balance' => $user->wallet ? $user->wallet->pending_balance : 0,
            'total_withdrawn' => $user->wallet ? $user->wallet->total_withdrawn : 0,
            'tasks_completed' => $user->tasks()->where('status', 'COMPLETED')->count(),
            'pending_withdrawals' => $user->withdrawals()->where('status', 'PENDING')->count(),
        ];

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'recentWithdrawals' => $recentWithdrawals,
            'recentTasks' => $recentTasks,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'required|string|max:20',
            'status' => 'required|in:ACTIVE,PENDING,SUSPENDED,BANNED,UNVERIFIED',
            'user_type' => 'required|in:USER,AGENT',
            'role' => 'required|in:0,1',
        ]);

        $user->update($request->only([
            'full_name',
            'email',
            'phone_number',
            'status',
            'user_type',
            'role',
        ]));

        return back()->with('success', 'User updated successfully');
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:ACTIVE,PENDING,SUSPENDED,BANNED,UNVERIFIED',
            'reason' => 'nullable|string|max:500',
        ]);

        $user->update(['status' => $request->status]);

        return back()->with('success', 'User status updated successfully');
    }

    public function banTask(Request $request, User $user)
    {
        $request->validate([
            'ban_until' => 'required|date|after:now',
            'reason' => 'required|string|max:500',
        ]);

        $user->update(['task_ban_until' => $request->ban_until]);

        return back()->with('success', 'User task access banned successfully');
    }

    public function clearTaskBan(User $user)
    {
        $user->update(['task_ban_until' => null]);

        return back()->with('success', 'Task ban cleared successfully');
    }

    public function verifyKyc(User $user)
    {
        $user->update(['kyc_verified_at' => now()]);

        return back()->with('success', 'KYC verified successfully');
    }

    public function adjustBalance(Request $request, User $user)
    {
        $request->validate([
            'type' => 'required|in:pending_balance,withdrawable_balance',
            'action' => 'required|in:add,subtract',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ]);

        if (!$user->wallet) {
            return back()->with('error', 'User has no wallet');
        }

        $amount = $request->amount;
        if ($request->action === 'subtract') {
            $amount = -$amount;
        }

        $user->wallet->increment($request->type, $amount);

        // Log transaction with all required fields
        $user->transactions()->create([
            'transaction_type' => $request->action === 'add' ? 'ADMIN_CREDIT' : 'ADMIN_DEBIT',
            'amount' => abs($amount),
            'balance_type' => strtoupper(str_replace('_balance', '', $request->type)),
            'is_credit' => $request->action === 'add',
            'description' => $request->reason,
            'status' => 'COMPLETED',
            'currency' => 'NGN',
            'priority' => 1,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Balance adjusted successfully');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account');
        }

        if ($user->role === 1) {
            return back()->with('error', 'Cannot delete admin accounts');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|in:ACTIVE,PENDING,SUSPENDED,BANNED,UNVERIFIED',
        ]);

        $count = User::whereIn('id', $request->user_ids)
            ->where('id', '!=', auth()->id()) // Prevent changing own status
            ->update(['status' => $request->status]);

        return back()->with('success', "{$count} user(s) updated successfully");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $count = User::whereIn('id', $request->user_ids)
            ->where('id', '!=', auth()->id()) // Prevent self-deletion
            ->where('role', '!=', 1) // Prevent admin deletion
            ->delete();

        return back()->with('success', "{$count} user(s) deleted successfully");
    }

    public function referrals(User $user)
    {
        // Build referral tree with deposits and withdrawals
        $tree = $this->buildReferralTree($user);

        // Calculate agent earnings if applicable
        $agentEarnings = null;
        if ($user->user_type === 'AGENT') {
            $agentEarnings = $this->calculateAgentEarnings($user);
        }

        return Inertia::render('Admin/Users/Referrals', [
            'user' => $user->load(['plan', 'rank', 'wallet']),
            'tree' => $tree,
            'agentEarnings' => $agentEarnings,
        ]);
    }

    public function tasks(User $user)
    {
        $tasks = $user->tasks()
            ->with('taskTemplate')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => $user->tasks()->count(),
            'completed' => $user->tasks()->where('status', 'COMPLETED')->count(),
            'pending' => $user->tasks()->where('status', 'PENDING')->count(),
            'in_progress' => $user->tasks()->where('status', 'IN_PROGRESS')->count(),
            'total_earned' => $user->tasks()->where('status', 'COMPLETED')->sum('reward_amount'),
        ];

        return Inertia::render('Admin/Users/Tasks', [
            'user' => $user->load(['plan', 'rank']),
            'tasks' => $tasks,
            'stats' => $stats,
        ]);
    }

    public function withdrawals(User $user)
    {
        $withdrawals = $user->withdrawals()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => $user->withdrawals()->count(),
            'pending' => $user->withdrawals()->where('status', 'PENDING')->count(),
            'approved' => $user->withdrawals()->where('status', 'APPROVED')->count(),
            'completed' => $user->withdrawals()->where('status', 'COMPLETED')->count(),
            'rejected' => $user->withdrawals()->where('status', 'REJECTED')->count(),
            'total_amount' => $user->withdrawals()->where('status', 'COMPLETED')->sum('amount_requested'),
        ];

        return Inertia::render('Admin/Users/Withdrawals', [
            'user' => $user->load(['plan', 'rank', 'wallet']),
            'withdrawals' => $withdrawals,
            'stats' => $stats,
        ]);
    }

    private function buildReferralTree(User $user)
    {
        $referrals = $user->directReferrals()
            ->with(['plan', 'wallet', 'transactions', 'rank', 'performance'])
            ->get();

        // Calculate total earned from wallet
        $totalEarned = $user->wallet ? $user->wallet->total_earned : 0;

        // Calculate total deposited (activation + upgrades)
        $totalDeposited = $this->calculateUserDeposits($user);

        return [
            'id' => $user->id,
            'name' => $user->full_name,
            'plan' => $user->plan?->display_name,
            'rank' => $user->rank?->name ?? 'No Rank',
            'status' => $user->status,
            'directReferrals' => $referrals->count(),
            'totalTeam' => $user->total_team_size ?? 0,
            'totalEarned' => $totalEarned,
            'totalDeposited' => $totalDeposited,
            'total_deposited' => $totalDeposited, // For modal
            'total_withdrawn' => $this->calculateUserWithdrawals($user),
            'children' => $referrals->map(function ($referral) {
                return $this->buildReferralTree($referral);
            })->toArray(),
        ];
    }

    private function calculateUserDeposits(User $user)
    {
        return $user->transactions()
            ->whereIn('transaction_type', ['ACTIVATION_PAYMENT', 'PLAN_UPGRADE'])
            ->where('status', 'COMPLETED')
            ->sum('amount');
    }

    private function calculateUserWithdrawals(User $user)
    {
        return $user->withdrawals()
            ->where('status', 'COMPLETED')
            ->sum('amount_requested');
    }

    private function calculateAgentEarnings(User $user)
    {
        // Get all users in the agent's DOWNLINE (excluding the agent themselves)
        $downlineUserIds = $this->getAllDownlineUserIds($user);

        $totalDeposits = \App\Models\Transaction::whereIn('user_id', $downlineUserIds)
            ->whereIn('transaction_type', ['ACTIVATION_PAYMENT', 'PLAN_UPGRADE'])
            ->where('status', 'COMPLETED')
            ->sum('amount');

        $totalWithdrawals = \App\Models\Withdrawal::whereIn('user_id', $downlineUserIds)
            ->where('status', 'COMPLETED')
            ->sum('amount_requested');

        return [
            'total_deposits' => $totalDeposits,
            'total_withdrawals' => $totalWithdrawals,
            'net_earnings' => $totalDeposits - $totalWithdrawals,
            'tree_size' => count($downlineUserIds),
        ];
    }

    private function getAllDownlineUserIds(User $user, &$ids = [])
    {
        // Only include referrals, NOT the user themselves
        foreach ($user->directReferrals as $referral) {
            $ids[] = $referral->id;
            $this->getAllDownlineUserIds($referral, $ids);
        }

        return $ids;
    }
}
