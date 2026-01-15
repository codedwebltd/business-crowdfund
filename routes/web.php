<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


use App\Models\User;
use App\Models\GlobalSetting;
use App\Services\PDFs\BasePDF;
use App\Helpers\CountryHelper;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $settings = \App\Models\GlobalSetting::first();

    // Get approved testimonials with user info (limited for performance)
    $testimonials = \App\Models\Testimonial::with('user')
        ->where('status', 'APPROVED')
        ->where('trash_testimonial', false)
        ->where('negative_testimonial', false)
        ->where('complaint_testimonial', false)
        ->orderBy('created_at', 'desc')
        ->limit(50) // Limit initial load for performance
        ->get()
        ->map(function ($testimonial) {
            return [
                'id' => $testimonial->id,
                'name' => $testimonial->name ?? $testimonial->user?->full_name ?? 'Anonymous',
                'message' => $testimonial->ai_corrected_message ?? $testimonial->message,
                'created_at' => $testimonial->created_at->diffForHumans(),
                'date' => $testimonial->created_at->format('M d, Y'),
            ];
        });

    // ========== EARNINGS SECTION: SERVER-SIDE TRANSACTION GENERATION ==========
    // You can manipulate these values to control what visitors see

    // Configuration - ADJUST THESE VALUES AS NEEDED
    $displayTransactionCount = 100;    // Shows "100 recent payouts" (can be 1000+)
    $fakeTransactionCount = 80;        // How many fake VIP transactions to ADD
    $totalPaidMultiplier = 18;         // Multiply real total by this (e.g., 15x)
    $fixedTotalPaidOut = null;         // Set a fixed amount (e.g., 500000000) or null to use multiplier

    // Helper function to abbreviate numbers (10000 → 10K, 1000000 → 1M)
    $abbreviateNumber = function($number) {
        $suffixes = ['', 'K', 'M', 'B', 'T'];
        $suffixIndex = 0;

        while ($number >= 1000 && $suffixIndex < count($suffixes) - 1) {
            $number /= 1000;
            $suffixIndex++;
        }

        // Format: if decimal is .0, show as integer, otherwise show 1 decimal
        $formatted = ($number == floor($number)) ? number_format($number, 0) : number_format($number, 1);
        return $formatted . $suffixes[$suffixIndex];
    };

    // VIP style names for FAKE transactions only
    $vipNames = [
        'VIP Member', 'Elite Earner', 'Premium User', 'Pro Trader', 'Gold Member',
        'Diamond User', 'Platinum Earner', 'Master Trader', 'Expert Member', 'Top Performer',
        'Star Earner', 'Champion User', 'Silver Elite', 'Bronze Pro', 'Emerald Member',
        'Ruby Earner', 'Sapphire VIP', 'Crystal Elite', 'Crown Member', 'Royal Earner',
        'Prime User', 'Ultra Member', 'Mega Earner', 'Super Pro', 'Alpha Trader',
        'Beta Member', 'Gamma Elite', 'Delta Pro', 'Omega VIP', 'Titan Earner',
        'Phoenix Member', 'Dragon Elite', 'Thunder Pro', 'Storm Trader', 'Lightning VIP',
        'Summit Member', 'Peak Earner', 'Zenith Pro', 'Apex Trader', 'Vertex VIP',
        'Pioneer Member', 'Legend Elite', 'Icon Pro', 'Maverick User', 'Ace Earner',
        'Boss Member', 'Chief Elite', 'Captain Pro', 'Admiral VIP', 'General Trader'
    ];

    $transactionTypes = [
        'TASK_COMPLETION', 'REFERRAL_BONUS', 'COMMISSION_EARNING', 'WITHDRAWAL_APPROVED',
        'RANK_BONUS', 'DAILY_EARNING', 'PERFORMANCE_BONUS', 'CRYPTO_PAYOUT',
        'MATRIX_BONUS', 'TEAM_COMMISSION', 'LEADERSHIP_BONUS', 'MATCHING_BONUS'
    ];

    $paymentMethods = ['bank_transfer', 'mobile_money', 'opay', 'palmpay', 'kuda', 'moniepoint'];

    // Get REAL transactions from database - KEEP REAL NAMES
    $realTransactions = \App\Models\Transaction::with('user:id,full_name')
        ->where('status', 'COMPLETED')
        ->where('is_credit', true)
        ->orderBy('created_at', 'desc')
        ->limit(30) // Get 30 real ones with REAL names
        ->get()
        ->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'user_name' => $transaction->user?->full_name ?? 'Anonymous User', // REAL NAME
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'transaction_type' => $transaction->transaction_type,
                'transaction_hash' => $transaction->transaction_hash,
                'created_at' => $transaction->created_at->diffForHumans(),
                'timestamp' => $transaction->created_at->format('M d, Y H:i'),
                'metadata' => $transaction->metadata,
                'description' => $transaction->description,
            ];
        })->toArray();

    // Generate FAKE transactions with VIP names (added alongside real ones)
    $fakeTransactions = [];
    $usedNames = []; // Track used names to avoid repetition

    // Get currency code from global settings (dynamic)
    $currencyCode = CountryHelper::getCurrencyCode($settings->country_of_operation ?? 'NGA');
    $countryPrefix = strtoupper(substr($settings->country_of_operation ?? 'NGA', 0, 2)); // NG, GH, KE, etc.

    for ($i = 0; $i < $fakeTransactionCount; $i++) {
        // Get unique VIP name (cycle through if needed)
        $availableNames = array_diff($vipNames, $usedNames);
        if (empty($availableNames)) {
            $usedNames = []; // Reset if we've used all names
            $availableNames = $vipNames;
        }
        $selectedName = $availableNames[array_rand($availableNames)];
        $usedNames[] = $selectedName;

        $isCrypto = rand(0, 100) < 25; // 25% chance of crypto transaction
        $amount = rand(5000, 500000); // 5,000 to 500,000 in local currency
        $minutesAgo = rand(1, 1440); // 1 minute to 24 hours ago

        $fakeTransactions[] = [
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'user_name' => $selectedName,
            'amount' => $amount,
            'currency' => $currencyCode, // Dynamic: NGN, GHS, KES, USD, etc.
            'transaction_type' => $transactionTypes[array_rand($transactionTypes)],
            'transaction_hash' => 'TXN-' . $countryPrefix . '-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'created_at' => $minutesAgo <= 60
                ? $minutesAgo . ' minutes ago'
                : ($minutesAgo <= 120 ? '1 hour ago' : floor($minutesAgo / 60) . ' hours ago'),
            'timestamp' => now()->subMinutes($minutesAgo)->format('M d, Y H:i'),
            'metadata' => $isCrypto ? [
                'crypto_address' => '0x' . strtoupper(\Illuminate\Support\Str::random(40)),
                'crypto_network' => ['ETH', 'BSC', 'TRON', 'POLYGON'][rand(0, 3)],
                'payment_method' => 'crypto'
            ] : [
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'bank_name' => ['GTBank', 'Access Bank', 'First Bank', 'UBA', 'Zenith Bank'][rand(0, 4)]
            ],
            'description' => 'Automated earning distribution',
        ];
    }

    // Combine real and fake transactions
    $allTransactions = array_merge($realTransactions, $fakeTransactions);
    shuffle($allTransactions); // Randomize order

    // Calculate totals
    $realTotal = \App\Models\Transaction::where('status', 'COMPLETED')
        ->where('is_credit', true)
        ->sum('amount');

    // Use fixed total if set, otherwise multiply real total
    $inflatedTotal = $fixedTotalPaidOut ?? ($realTotal * $totalPaidMultiplier);

    // Calculate fake transaction count for display
    $displayCount = $displayTransactionCount > 0 ? $displayTransactionCount : count($allTransactions);

    // Get currency symbol from global settings (dynamic, not hardcoded)
    $currencySymbol = CountryHelper::getCurrencySymbol($settings->country_of_operation ?? 'NGA');

    return Inertia::render('Welcome', [
        'settings' => $settings,
        'testimonials' => $testimonials,
        'transactions' => array_slice($allTransactions, 0, 100), // Send up to 100 mixed
        'totalPaidOut' => $inflatedTotal,                        // Raw number for formatting
        'totalPaidOutFormatted' => $currencySymbol . $abbreviateNumber($inflatedTotal), // Abbreviated: ₦1.2M, $500K, etc.
        'transactionCount' => $displayCount,                     // Manipulated count for display
        'currencySymbol' => $currencySymbol,                     // For frontend use
    ]);
});

// Auth Routes
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
Route::post('/register/verify-otp', [App\Http\Controllers\Auth\RegisterController::class, 'verifyOTP']);
Route::post('/register/resend-otp', [App\Http\Controllers\Auth\RegisterController::class, 'resendOTP']);

// Login Routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// 2FA Challenge Routes
Route::get('/2fa/verify', [App\Http\Controllers\Auth\TwoFactorController::class, 'show'])->name('2fa.verify');
Route::post('/2fa/verify', [App\Http\Controllers\Auth\TwoFactorController::class, 'verify']);
Route::post('/2fa/verify-backup', [App\Http\Controllers\Auth\TwoFactorController::class, 'verifyBackup']);

// QR Code Generation (Public)
Route::get('/qr-code', [App\Http\Controllers\QRCodeController::class, 'generate']);

// Protected Routes with Fraud Detection & Role Redirect
Route::middleware(['auth', 'role.redirect', 'fraud.detect', 'has.plan'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user()->load(['plan', 'performance']);
        $settings = \App\Models\GlobalSetting::first();
        $plans = \App\Models\Plan::where('is_active', true)->orderBy('order')->get();

        // If user doesn't have a plan, check for pending payment
        if (!$user->plan_id) {
            $pendingTransaction = \App\Models\Transaction::where('user_id', $user->id)
                ->where('transaction_type', 'ACTIVATION_PAYMENT')
                ->where('status', 'AWAITING_APPROVAL')
                ->latest()
                ->first();

            if ($pendingTransaction) {
                // Show pending approval screen
                return Inertia::render('Payment/PendingApproval', [
                    'transactionId' => $pendingTransaction->transaction_hash,
                    'planName' => $pendingTransaction->metadata['plan_name'] ?? 'Selected Plan',
                    'createdAt' => $pendingTransaction->created_at->toIso8601String(),
                    'supportEmail' => $settings->support_email ?? null,
                    'supportPhone' => $settings->support_phone ?? null,
                    'supportWhatsapp' => $settings->support_whatsapp ?? null,
                ]);
            }

            // Show plan selection
            return Inertia::render('Dashboard/PlanSelection', [
                'plans' => $plans,
                'settings' => $settings,
                'currencySymbol' => $settings->currency_symbol ?? '₦',
            ]);
        }

        // Get user stats
        $stats = [
            'totalEarnings' => $user->wallet ? $user->wallet->total_earned : 0,
            'availableBalance' => $user->wallet ? $user->wallet->withdrawable_balance : 0,
            'pendingBalance' => $user->wallet ? $user->wallet->pending_balance : 0,
            'totalWithdrawn' => $user->wallet ? $user->wallet->total_withdrawn : 0,
            'pendingWithdrawal' => $user->withdrawals()->where('status', 'pending')->sum('amount_requested'),
            'tasksCompleted' => $user->tasks()->where('status', 'COMPLETED')->count(),
            'tasksCompletedToday' => $user->tasks()->where('status', 'COMPLETED')->whereDate('completed_at', today())->count(),
            'totalReferrals' => $user->directReferrals()->count(),
        ];

        // Check for recent fraud incidents (for reCAPTCHA trigger)
        $recentFraudCount = \App\Models\FraudIncident::where('user_id', $user->id)
            ->where('created_at', '>', now()->subDays(7))
            ->count();

        $hasRecentFraud = $recentFraudCount > 0;

        // Get today's tasks
        $tasks = \App\Models\UserTask::with('taskTemplate')
            ->where('user_id', $user->id)
            ->whereDate('assigned_at', today())
            ->orderBy('status')
            ->get();

        // Announcements
        $announcements = \App\Models\Announcement::active()
            ->forUser($user)
            ->notDismissedBy($user)
            ->ordered()
            ->get();

        // Token Rate (if fluctuation enabled)
        $latestHistory = \App\Models\TokenRateHistory::latest()->first();
        $tokenFluctuationEnabled = $settings->token_settings['fluctuation_enabled'] ?? false;
        $tokenRate = [
            'token_price' => $settings->token_settings['token_price'] ?? 850,
            'withdrawal_rate' => (float) ($settings->withdrawal_rate ?? 0.68),
            'trend' => $latestHistory->trend ?? 'stable',
            'trend_percentage' => $latestHistory ? $latestHistory->getTrendPercentage() : '0%',
            'is_good_time' => $latestHistory ? $latestHistory->isGoodWithdrawalTime() : false,
        ];

        // Check for pending upgrade transaction
        $pendingUpgrade = \App\Models\Transaction::where('user_id', $user->id)
            ->where('transaction_type', 'PLAN_UPGRADE')
            ->where('status', 'AWAITING_APPROVAL')
            ->latest()
            ->first();

        // Check if user qualifies for plan upgrade
        $qualifiedPlan = null;
        $upgradeOffer = null;
        if ($user->performance && !$pendingUpgrade) {
            $starRating = $user->performance->star_rating ?? 1;

            // Get qualified plan using order field (dynamic, won't break if plan names change)
            // 1 star = Order 1, 2 stars = Order 2, etc.
            $qualifiedPlan = \App\Models\Plan::where('order', $starRating)->first();

            // Check if qualified plan is higher than current plan
            if ($qualifiedPlan && $qualifiedPlan->order > $user->plan->order) {
                $discount = $settings->plan_upgrade_discount_percentage ?? 20;
                $originalPrice = $qualifiedPlan->price;
                $discountedPrice = $originalPrice * (1 - $discount / 100);
                $savings = $originalPrice - $discountedPrice;

                $upgradeOffer = [
                    'qualified_plan' => $qualifiedPlan,
                    'discount_percentage' => $discount,
                    'original_price' => $originalPrice,
                    'discounted_price' => $discountedPrice,
                    'savings' => $savings,
                    'star_rating' => $starRating,
                ];
            }
        }

        return Inertia::render('User/Dashboard', [
            'settings' => $settings,
            'auth' => [
                'user' => $user
            ],
            'user' => $user,
            'plans' => $plans,
            'stats' => $stats,
            'tasks' => $tasks,
            'announcements' => $announcements,
            'tokenFluctuationEnabled' => $tokenFluctuationEnabled,
            'tokenRate' => $tokenRate,
            'hasRecentFraud' => $hasRecentFraud, // For reCAPTCHA trigger
            'upgradeOffer' => $upgradeOffer, // Plan upgrade offer if qualified
            'pendingUpgrade' => $pendingUpgrade ? [
                'transaction_hash' => $pendingUpgrade->transaction_hash,
                'plan_name' => $pendingUpgrade->metadata['new_plan_name'] ?? 'Unknown',
                'amount' => $pendingUpgrade->amount,
                'created_at' => $pendingUpgrade->created_at->toIso8601String(),
                'metadata' => $pendingUpgrade->metadata,
            ] : null,
        ]);
    })->name('dashboard');

    // Announcements
    Route::post('/announcements/dismiss', function(\Illuminate\Http\Request $request) {
        $request->validate(['announcement_id' => 'required|exists:announcements,id']);

        auth()->user()->dismissedAnnouncements()->syncWithoutDetaching([
            $request->announcement_id => ['dismissed_at' => now()]
        ]);

        return back();
    });

    // Referrals
    Route::get('/referrals', [App\Http\Controllers\User\ReferralController::class, 'index'])->name('referrals');
    Route::get('/hardwork-stats', [App\Http\Controllers\User\ReferralController::class, 'hardworkStats'])->name('hardwork-stats');

    // Payment Routes
    Route::post('/payment/initiate', [App\Http\Controllers\PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::post('/payment/confirm', [App\Http\Controllers\PaymentController::class, 'confirm'])->name('payment.confirm');
    Route::get('/payment/view', [App\Http\Controllers\PaymentController::class, 'viewPaymentDetails'])->name('payment.view');

    // Plan Upgrade Routes
    Route::post('/plan/upgrade/payment', [App\Http\Controllers\PlanUpgradeController::class, 'showPayment'])->name('plan.upgrade.payment');
    Route::post('/plan/upgrade/confirm', [App\Http\Controllers\PlanUpgradeController::class, 'confirmPayment'])->name('plan.upgrade.confirm');

    // User Tasks
    Route::post('/tasks/{id}/start', [App\Http\Controllers\User\TaskController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{id}/complete', [App\Http\Controllers\User\TaskController::class, 'complete'])->name('tasks.complete');

    // Withdrawal Routes
    Route::get('/withdrawal', [App\Http\Controllers\User\WithdrawalController::class, 'index'])->name('withdrawal');
    Route::post('/withdrawal', [App\Http\Controllers\User\WithdrawalController::class, 'store'])->name('withdrawal.store');
    Route::post('/testimonials', [App\Http\Controllers\User\TestimonialController::class, 'store'])->name('testimonials.store');

    // KYC Routes
    Route::post('/api/kyc/upload', [App\Http\Controllers\User\KycController::class, 'upload'])->name('kyc.upload');
    Route::post('/kyc/submit', [App\Http\Controllers\User\KycController::class, 'submit'])->name('kyc.submit');
    Route::get('/api/kyc/status', [App\Http\Controllers\User\KycController::class, 'status'])->name('kyc.status');

    // Transaction History Routes
    Route::get('/transactions', [App\Http\Controllers\User\TransactionController::class, 'index'])->name('transactions');
    Route::get('/transactions/{id}', [App\Http\Controllers\User\TransactionController::class, 'show'])->name('transactions.show');

    // Settings Routes
    Route::get('/settings', [App\Http\Controllers\Auth\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/profile', [App\Http\Controllers\Auth\SettingsController::class, 'updateProfile']);
    Route::post('/settings/notifications', [App\Http\Controllers\Auth\SettingsController::class, 'updateNotifications']);
    Route::post('/settings/password', [App\Http\Controllers\Auth\SettingsController::class, 'updatePassword']);
    Route::post('/settings/delete-account', [App\Http\Controllers\Auth\SettingsController::class, 'deleteAccount']);
    Route::post('/settings/2fa/enable', [App\Http\Controllers\Auth\SettingsController::class, 'enable2FA']);
    Route::post('/settings/2fa/verify', [App\Http\Controllers\Auth\SettingsController::class, 'verify2FA']);
    Route::post('/settings/2fa/disable', [App\Http\Controllers\Auth\SettingsController::class, 'disable2FA']);
    Route::post('/settings/2fa/cancel', [App\Http\Controllers\Auth\SettingsController::class, 'cancel2FA']);
    Route::post('/settings/2fa/backup-codes', [App\Http\Controllers\Auth\SettingsController::class, 'regenerateBackupCodes']);
    Route::post('/settings/payment-methods', [App\Http\Controllers\Auth\SettingsController::class, 'updatePaymentMethods']);

    // Notification Routes
    Route::get('/notifications', [App\Http\Controllers\User\NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\User\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\User\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [App\Http\Controllers\User\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/delete-all', [App\Http\Controllers\User\NotificationController::class, 'deleteAll'])->name('notifications.delete-all');

    // Admin Routes (Role 1 only)
    Route::prefix('admin')->middleware('admin.access')->group(function () {
        Route::get('/', function () {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized');
            }

            $settings = \App\Models\GlobalSetting::first();

            // Efficient aggregated queries for dashboard stats
            $stats = [
                'total_users' => \App\Models\User::count(),
                'active_users' => \App\Models\User::where('status', 'ACTIVE')->count(),
                'pending_users' => \App\Models\User::where('status', 'PENDING')->count(),
                'total_withdrawals' => \App\Models\Withdrawal::where('status', 'APPROVED')->sum('amount_requested'),
                'pending_withdrawals' => \App\Models\Withdrawal::where('status', 'PENDING')->count(),
                'completed_tasks_today' => \App\Models\UserTask::whereDate('completed_at', today())->whereNotNull('completed_at')->count(),
                'total_earnings_paid' => \App\Models\Withdrawal::where('status', 'APPROVED')->sum('amount_requested'),
            ];

            // Last 7 days data for charts (aggregated, not individual records)
            $chartData = [
                'daily_registrations' => \App\Models\User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count', 'date'),
                'daily_tasks' => \App\Models\UserTask::selectRaw('DATE(completed_at) as date, COUNT(*) as count')
                    ->where('completed_at', '>=', now()->subDays(7))
                    ->whereNotNull('completed_at')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count', 'date'),
                'daily_withdrawals' => \App\Models\Withdrawal::selectRaw('DATE(requested_at) as date, SUM(amount_requested) as total')
                    ->where('requested_at', '>=', now()->subDays(7))
                    ->where('status', 'APPROVED')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('total', 'date'),
            ];

            // User distribution by status (for pie chart)
            $usersByStatus = \App\Models\User::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            return Inertia::render('Admin/Dashboard', [
                'settings' => $settings,
                'stats' => $stats,
                'chartData' => $chartData,
                'usersByStatus' => $usersByStatus,
            ]);
        })->name('admin.dashboard');

        Route::get('/settings', function () {
            if (!auth()->user()->isAdmin()) abort(403);

            $settings = \App\Models\GlobalSetting::first();
            $countries = \App\Helpers\CountryHelper::forDropdown();
            $plans = \App\Models\Plan::select('id', 'name')->get();
            $ranks = \App\Models\Rank::select('id', 'name')->get();

            return Inertia::render('Admin/Settings/Index', [
                'settings' => $settings,
                'countries' => $countries,
                'plans' => $plans,
                'ranks' => $ranks,
            ]);
        })->name('admin.settings');

        Route::post('/settings/upload-logo', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'uploadLogo']);
        Route::post('/settings/app-config', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateAppConfig']);
        Route::post('/settings/referral', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateReferral']);
        Route::post('/settings/tasks', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateTasks']);
        Route::post('/settings/ranks', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateRanks']);
        Route::post('/settings/star-ratings', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateStarRatings']);
        Route::post('/settings/plan-upgrades', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updatePlanUpgrades']);
        Route::post('/settings/financial', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateFinancial']);
        Route::post('/settings/fraud', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateFraud']);
        Route::post('/settings/token', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateToken']);
        Route::post('/settings/kyc', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateKYC']);
        Route::post('/settings/system', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateSystem']);
        Route::post('/settings/notifications', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateNotifications']);
        Route::post('/settings/integrations', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateIntegrations']);
        Route::post('/settings/ai', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateAI']);
        Route::post('/settings/security', [\App\Http\Controllers\Admin\GlobalSettingsController::class, 'updateSecurity']);

        // Task Templates
        Route::get('/tasks', [\App\Http\Controllers\Admin\TaskTemplateController::class, 'index'])->name('admin.tasks');

        // Rank Management
        Route::get('/ranks', [\App\Http\Controllers\Admin\RankController::class, 'index'])->name('admin.ranks');
        Route::post('/ranks', [\App\Http\Controllers\Admin\RankController::class, 'store'])->name('admin.ranks.store');
        Route::post('/ranks/{rank}/update', [\App\Http\Controllers\Admin\RankController::class, 'update'])->name('admin.ranks.update');
        Route::post('/ranks/{rank}/toggle', [\App\Http\Controllers\Admin\RankController::class, 'toggleStatus'])->name('admin.ranks.toggle');
        Route::delete('/ranks/{rank}', [\App\Http\Controllers\Admin\RankController::class, 'destroy'])->name('admin.ranks.destroy');

        // Plan Management (Subscriptions)
        Route::get('/subscriptions', [\App\Http\Controllers\Admin\PlanController::class, 'index'])->name('admin.subscriptions');
        Route::post('/subscriptions', [\App\Http\Controllers\Admin\PlanController::class, 'store'])->name('admin.subscriptions.store');
        Route::post('/subscriptions/{plan}/update', [\App\Http\Controllers\Admin\PlanController::class, 'update'])->name('admin.subscriptions.update');
        Route::post('/subscriptions/{plan}/toggle', [\App\Http\Controllers\Admin\PlanController::class, 'toggleStatus'])->name('admin.subscriptions.toggle');
        Route::post('/subscriptions/{plan}/toggle-popular', [\App\Http\Controllers\Admin\PlanController::class, 'togglePopular'])->name('admin.subscriptions.toggle-popular');
        Route::delete('/subscriptions/{plan}', [\App\Http\Controllers\Admin\PlanController::class, 'destroy'])->name('admin.subscriptions.destroy');

        // User Management
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
        Route::post('/users/{user}/update', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
        Route::get('/users/{user}/referrals', [\App\Http\Controllers\Admin\UserController::class, 'referrals'])->name('admin.users.referrals');
        Route::get('/users/{user}/tasks', [\App\Http\Controllers\Admin\UserController::class, 'tasks'])->name('admin.users.tasks');
        Route::get('/users/{user}/withdrawals', [\App\Http\Controllers\Admin\UserController::class, 'withdrawals'])->name('admin.users.withdrawals');
        Route::post('/users/{user}/status', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('admin.users.status');
        Route::post('/users/{user}/ban-task', [\App\Http\Controllers\Admin\UserController::class, 'banTask'])->name('admin.users.ban-task');
        Route::post('/users/{user}/clear-task-ban', [\App\Http\Controllers\Admin\UserController::class, 'clearTaskBan'])->name('admin.users.clear-task-ban');
        Route::post('/users/{user}/verify-kyc', [\App\Http\Controllers\Admin\UserController::class, 'verifyKyc'])->name('admin.users.verify-kyc');
        Route::post('/users/{user}/adjust-balance', [\App\Http\Controllers\Admin\UserController::class, 'adjustBalance'])->name('admin.users.adjust-balance');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/users/bulk-status', [\App\Http\Controllers\Admin\UserController::class, 'bulkStatus'])->name('admin.users.bulk-status');
        Route::post('/users/bulk-delete', [\App\Http\Controllers\Admin\UserController::class, 'bulkDelete'])->name('admin.users.bulk-delete');

        // Content Pool Management
        Route::get('/content-pool', [\App\Http\Controllers\Admin\ContentPoolController::class, 'index'])->name('admin.content-pool');
        Route::post('/content-pool/{id}/toggle', [\App\Http\Controllers\Admin\ContentPoolController::class, 'toggleActive'])->name('admin.content-pool.toggle');
        Route::delete('/content-pool/{id}', [\App\Http\Controllers\Admin\ContentPoolController::class, 'destroy'])->name('admin.content-pool.destroy');
        Route::post('/content-pool/{id}/reset-usage', [\App\Http\Controllers\Admin\ContentPoolController::class, 'resetUsage'])->name('admin.content-pool.reset');
        Route::post('/tasks/{id}/approve', [\App\Http\Controllers\Admin\TaskTemplateController::class, 'approve']);
        Route::post('/tasks/{id}/deactivate', [\App\Http\Controllers\Admin\TaskTemplateController::class, 'deactivate']);
        Route::delete('/tasks/{id}', [\App\Http\Controllers\Admin\TaskTemplateController::class, 'destroy']);

        // Fraud Incidents
        Route::get('/fraud-incidents', [\App\Http\Controllers\Admin\FraudIncidentController::class, 'index'])->name('admin.fraud-incidents');
        Route::post('/fraud-incidents/{userId}/unsuspend', [\App\Http\Controllers\Admin\FraudIncidentController::class, 'unsuspend']);
        Route::post('/fraud-incidents/{id}/resolve', [\App\Http\Controllers\Admin\FraudIncidentController::class, 'resolve']);

        // Transactions
        Route::get('/transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('admin.transactions');
        Route::post('/transactions/{id}/approve', [\App\Http\Controllers\Admin\TransactionController::class, 'approve']);
        Route::post('/transactions/{id}/reject', [\App\Http\Controllers\Admin\TransactionController::class, 'reject']);

        // Withdrawals
        Route::get('/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('admin.withdrawals');
        Route::post('/withdrawals/{id}/approve', [\App\Http\Controllers\Admin\WithdrawalController::class, 'approve']);
        Route::post('/withdrawals/{id}/reject', [\App\Http\Controllers\Admin\WithdrawalController::class, 'reject']);
        Route::post('/withdrawals/{id}/processing', [\App\Http\Controllers\Admin\WithdrawalController::class, 'markProcessing']);

        // Testimonials
        Route::get('/testimonials', [\App\Http\Controllers\Admin\TestimonialController::class, 'index'])->name('admin.testimonials');
        Route::post('/testimonials/{id}/approve', [\App\Http\Controllers\Admin\TestimonialController::class, 'approve']);
        Route::post('/testimonials/{id}/reject', [\App\Http\Controllers\Admin\TestimonialController::class, 'reject']);
        Route::post('/testimonials/generate-ai', [\App\Http\Controllers\Admin\TestimonialController::class, 'generateWithAI'])->name('admin.testimonials.generate-ai');
        Route::post('/testimonials/bulk-publish', [\App\Http\Controllers\Admin\TestimonialController::class, 'bulkPublish'])->name('admin.testimonials.bulk-publish');

        // KYC Verifications
        Route::get('/kyc', [\App\Http\Controllers\Admin\KycController::class, 'index'])->name('admin.kyc');
        Route::post('/kyc/{id}/approve', [\App\Http\Controllers\Admin\KycController::class, 'approve']);
        Route::post('/kyc/{id}/reject', [\App\Http\Controllers\Admin\KycController::class, 'reject']);
        Route::post('/kyc/{id}/delete-document', [\App\Http\Controllers\Admin\KycController::class, 'deleteDocument']);

        // Documentation
        Route::get('/documentation', [\App\Http\Controllers\Admin\DocumentationController::class, 'index'])->name('admin.documentation.index');
        Route::get('/documentation/{slug}', [\App\Http\Controllers\Admin\DocumentationController::class, 'show'])->name('admin.documentation.show');

        // Liquidity & Earnings
        Route::get('/liquidity', [\App\Http\Controllers\Admin\LiquidityController::class, 'index'])->name('admin.liquidity.index');
        Route::delete('/liquidity/{id}', [\App\Http\Controllers\Admin\LiquidityController::class, 'destroy'])->name('admin.liquidity.destroy');

        // Support Management
        Route::get('/support', [\App\Http\Controllers\Admin\SupportController::class, 'index'])->name('admin.support.index');
        Route::get('/support/stats', [\App\Http\Controllers\Admin\SupportController::class, 'stats'])->name('admin.support.stats');
        Route::get('/support/{ticketId}', [\App\Http\Controllers\Admin\SupportController::class, 'show'])->name('admin.support.show');
        Route::post('/support/{ticketId}/message', [\App\Http\Controllers\Admin\SupportController::class, 'sendMessage']);
        Route::get('/support/{ticketId}/messages', [\App\Http\Controllers\Admin\SupportController::class, 'getMessages']);
        Route::post('/support/{ticketId}/typing', [\App\Http\Controllers\Admin\SupportController::class, 'typing']);
        Route::post('/support/{ticketId}/status', [\App\Http\Controllers\Admin\SupportController::class, 'updateStatus']);
        Route::post('/support/{ticketId}/priority', [\App\Http\Controllers\Admin\SupportController::class, 'updatePriority']);
        Route::post('/support/{ticketId}/assign', [\App\Http\Controllers\Admin\SupportController::class, 'assignTicket']);
    });
});






Route::get('/demo', function () {

    $settings = GlobalSetting::first() ?? (object) [
        'app_name' => 'CrowdPower',
        'country_of_operation' => 'NG',
        'daily_earning_average' => 850,
        'referral_levels_depth' => 10,
        'minimum_withdrawal' => 5000,
        'total_users' => '10,000+',
    ];

    // ✅ REAL User model (not persisted)
    $user = new User([
        'full_name'     => 'John Doe',
        'email'         => 'john@example.com',
        'phone_number'  => '+234 800 000 0000',
        'referral_code' => 'CPWR1234',
        'status'        => 'PENDING',
    ]);

    // Fake plan & rank
    $plan = (object) [
        'name' => 'Starter',
        'max_daily_tasks' => 20,
        'task_earning_multiplier' => 1.5,
        'price' => 15000,
    ];

    $rank = (object) ['name' => 'Bronze'];

    $user->plan = $plan;
    $user->rank = $rank;

    // Anonymous class now receives a REAL User
    $pdfBase = new class($user) extends BasePDF {
        public function generate(): \Illuminate\Http\Response {}
        protected function getViewName(): string { return ''; }
        protected function getData(): array { return []; }
        public function css(): string { return $this->getBaseCSS(); }
    };

    return view('pdfs.welcome-contract', [
        'documentTitle'     => 'Partnership Agreement',
        'contractDate'      => now()->format('F d, Y'),
        'companyName'       => $settings->app_name,
        'userName'          => $user->full_name,
        'userEmail'         => $user->email,
        'userPhone'         => $user->phone_number,
        'referralCode'      => $user->referral_code,
        'planName'          => $plan->name,
        'rankName'          => $rank->name,
        'maxDailyTasks'     => $plan->max_daily_tasks,
        'taskMultiplier'    => $plan->task_earning_multiplier,
        'activationAmount' => CountryHelper::formatMoney(
                                $plan->price,
                                $settings->country_of_operation
                             ),

        // Required by Blade
        'settings'  => $settings,
        'user'      => $user,
        'watermark' => 'OFFICIAL CONTRACT',
        'logo'      => 'https://dummyimage.com/120x120/7c3aed/ffffff&text=CP',
        'baseCSS'   => $pdfBase->css(),
    ]);
});



Route::get('/demo2', function () {

    // Settings (real or fallback)
    $settings = GlobalSetting::first() ?? (object) [
        'app_name' => 'CrowdPower',
        'app_url' => 'crowdpower.example',
        'country_of_operation' => 'NG',
        'pending_balance_maturation_hours' => 72,
        'referral_levels_depth' => 40,
        'minimum_withdrawal' => 5000,
        'maximum_withdrawal' => 50000,
        'kyc_withdrawal_threshold' => 50000,
        'support_email' => 'support@crowdpower.example',
        'support_phone' => '+234 800 000 0000',
        'support_whatsapp' => '+234 800 000 0000',
    ];

    // Fake user (REAL model, not saved)
    $user = new User([
        'id' => 12345,
        'full_name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone_number' => '+234 801 234 5678',
        'status' => 'ACTIVE',
    ]);

    // Access BasePDF CSS safely
    $pdfBase = new class($user) extends BasePDF {
        public function generate(): \Illuminate\Http\Response {}
        protected function getViewName(): string { return ''; }
        protected function getData(): array { return []; }
        public function css(): string { return $this->getBaseCSS(); }
    };

    return view('pdfs.terms-and-conditions', [
        // Document metadata
        'documentTitle' => 'Terms and Conditions',
        'effectiveDate' => now()->format('F d, Y'),

        // Required globals
        'companyName' => $settings->app_name,
        'settings'    => $settings,
        'user'        => $user,

        // Styling / branding
        'watermark' => 'TERMS & CONDITIONS',
        'logo'      => 'https://dummyimage.com/120x120/7c3aed/ffffff&text=CP',
        'baseCSS'   => $pdfBase->css(),
    ]);
});

// ========== SUPPORT SYSTEM ROUTES ==========
// Public Support API (for widget - works for guests and authenticated users)
Route::prefix('api/support')->group(function () {
    Route::get('/ticket', [App\Http\Controllers\SupportController::class, 'getOrCreateTicket']);
    Route::post('/ticket', [App\Http\Controllers\SupportController::class, 'startTicket']);
    Route::get('/tickets', [App\Http\Controllers\SupportController::class, 'getTickets']);
    Route::get('/ticket/{ticketId}/messages', [App\Http\Controllers\SupportController::class, 'getMessages']);
    Route::post('/ticket/{ticketId}/message', [App\Http\Controllers\SupportController::class, 'sendMessage']);
    Route::post('/ticket/{ticketId}/typing', [App\Http\Controllers\SupportController::class, 'typing']);
    Route::post('/ticket/{ticketId}/close', [App\Http\Controllers\SupportController::class, 'closeTicket']);
    Route::post('/ticket/{ticketId}/rate', [App\Http\Controllers\SupportController::class, 'rateTicket']);
});

// Support Pages (Inertia)
Route::get('/support', [App\Http\Controllers\SupportController::class, 'index'])->name('support');
Route::get('/support/{ticketId}', [App\Http\Controllers\SupportController::class, 'show'])->name('support.show');

// Admin Command Control Routes
Route::middleware(['auth', 'role.redirect'])->prefix('admin')->group(function () {
    Route::get('/commands', [App\Http\Controllers\Admin\CommandControlController::class, 'index'])->name('admin.commands');
    Route::post('/commands/execute', [App\Http\Controllers\Admin\CommandControlController::class, 'execute']);
    Route::get('/commands/batch/{batchId}', [App\Http\Controllers\Admin\CommandControlController::class, 'getBatchStatus']);
    Route::post('/commands/retry/{id}', [App\Http\Controllers\Admin\CommandControlController::class, 'retryFailedJob']);
    Route::post('/commands/clear-failed', [App\Http\Controllers\Admin\CommandControlController::class, 'clearFailedJobs']);
    Route::post('/commands/clear-batches', [App\Http\Controllers\Admin\CommandControlController::class, 'clearBatches']);
    Route::get('/commands/laravel-log', [App\Http\Controllers\Admin\CommandControlController::class, 'getLaravelLog']);
    Route::post('/commands/clear-log', [App\Http\Controllers\Admin\CommandControlController::class, 'clearLaravelLog']);

    // Announcements
    Route::get('/announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->name('admin.announcements.index');
    Route::get('/announcements/create', [App\Http\Controllers\Admin\AnnouncementController::class, 'create'])->name('admin.announcements.create');
    Route::post('/announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->name('admin.announcements.store');
    Route::get('/announcements/{announcement}/edit', [App\Http\Controllers\Admin\AnnouncementController::class, 'edit'])->name('admin.announcements.edit');
    Route::put('/announcements/{announcement}', [App\Http\Controllers\Admin\AnnouncementController::class, 'update'])->name('admin.announcements.update');
    Route::delete('/announcements/{announcement}', [App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');
    Route::post('/announcements/{announcement}/send', [App\Http\Controllers\Admin\AnnouncementController::class, 'sendNotifications'])->name('admin.announcements.send');
    Route::post('/announcements/generate-ai', [App\Http\Controllers\Admin\AnnouncementController::class, 'generateWithAI']);

    // Git Operations
    Route::post('/git/push', [App\Http\Controllers\Admin\GitController::class, 'push']);
    Route::get('/git/status', [App\Http\Controllers\Admin\GitController::class, 'status']);
    Route::get('/git/log', [App\Http\Controllers\Admin\GitController::class, 'log']);
    Route::get('/git/info', [App\Http\Controllers\Admin\GitController::class, 'info']);
    Route::get('/git/uncommitted', [App\Http\Controllers\Admin\GitController::class, 'uncommitted']);
    Route::post('/git/generate-commit', [App\Http\Controllers\Admin\GitController::class, 'generateCommitMessage']);
});
