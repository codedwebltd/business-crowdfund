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
    $settings = \App\Models\GlobalSetting::get();
    return Inertia::render('Welcome', [
        'settings' => $settings
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
        $user = auth()->user()->load('plan');
        $settings = \App\Models\GlobalSetting::get();
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

    // Payment Routes
    Route::post('/payment/initiate', [App\Http\Controllers\PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::post('/payment/confirm', [App\Http\Controllers\PaymentController::class, 'confirm'])->name('payment.confirm');
    Route::get('/payment/view', [App\Http\Controllers\PaymentController::class, 'viewPaymentDetails'])->name('payment.view');

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
    Route::prefix('admin')->group(function () {
        Route::get('/', function () {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized');
            }

            $settings = \App\Models\GlobalSetting::get();

            return Inertia::render('Admin/Dashboard', [
                'settings' => $settings,
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

        // Testimonials
        Route::get('/testimonials', [\App\Http\Controllers\Admin\TestimonialController::class, 'index'])->name('admin.testimonials');
        Route::post('/testimonials/{id}/approve', [\App\Http\Controllers\Admin\TestimonialController::class, 'approve']);
        Route::post('/testimonials/{id}/reject', [\App\Http\Controllers\Admin\TestimonialController::class, 'reject']);

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

// Admin Command Control Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/commands', [App\Http\Controllers\Admin\CommandControlController::class, 'index'])->name('admin.commands');
    Route::post('/commands/execute', [App\Http\Controllers\Admin\CommandControlController::class, 'execute']);
    Route::get('/commands/batch/{batchId}', [App\Http\Controllers\Admin\CommandControlController::class, 'getBatchStatus']);
    Route::post('/commands/retry/{id}', [App\Http\Controllers\Admin\CommandControlController::class, 'retryFailedJob']);
    Route::post('/commands/clear-failed', [App\Http\Controllers\Admin\CommandControlController::class, 'clearFailedJobs']);
});
