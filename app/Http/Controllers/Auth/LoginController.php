<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FraudDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;

class LoginController extends Controller
{
    protected FraudDetectionService $fraudService;

    public function __construct(FraudDetectionService $fraudService)
    {
        $this->fraudService = $fraudService;
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        $settings = \App\Models\GlobalSetting::first();

        return Inertia::render('Auth/Login', [
            'settings' => \App\Models\GlobalSetting::get(),
            'smsEnabled' => $settings->sms_notifications_enabled ?? false,
        ]);
    }

    /**
     * Handle login with fraud detection
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => ['required', 'string'], // Can be email or phone
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Please enter your email or phone number.',
            'password.required' => 'Please enter your password.',
        ]);

        // Rate limiting - 5 attempts per minute
        $throttleKey = strtolower($validated['login']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            Log::warning('🚫 LOGIN RATE LIMIT EXCEEDED', [
                'login' => $validated['login'],
                'ip' => $request->ip(),
                'seconds_remaining' => $seconds,
            ]);

            return back()->withErrors([
                'login' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        // Find user by email or phone
        $user = User::where('email', $validated['login'])
            ->orWhere('phone_number', $validated['login'])
            ->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            Log::warning('❌ FAILED LOGIN ATTEMPT', [
                'login' => $validated['login'],
                'ip' => $request->ip(),
                'user_found' => $user ? 'yes' : 'no',
            ]);

            return back()->withErrors([
                'login' => 'Invalid credentials. Please check your email/phone and password.',
            ]);
        }

        // Check if account is suspended or banned
        if (in_array($user->status, ['BANNED', 'SUSPENDED'])) {
            Log::warning('🚫 SUSPENDED/BANNED USER LOGIN ATTEMPT', [
                'user_id' => $user->id,
                'user_name' => $user->full_name,
                'status' => $user->status,
                'ip' => $request->ip(),
            ]);

            $reason = $user->status === 'SUSPENDED'
                ? 'Your account has been suspended due to repeated VPN usage violations. Please contact support for assistance.'
                : 'Your account has been permanently banned. Please contact support.';

            return back()->withErrors([
                'login' => $reason,
            ]);
        }

        // Fraud Detection on Login
        $deviceData = $this->fraudService->extractLocationData();
        $currentIP = $deviceData['ip'] ?? $request->ip();

        Log::info('🔐 LOGIN ATTEMPT', [
            'user_id' => $user->id,
            'user_name' => $user->full_name,
            'ip' => $currentIP,
            'status' => $user->status,
        ]);

        // Queue async VPN detection (no login delay - uses background job)
        \App\Jobs\CheckVpnFraud::dispatch($user, $currentIP, 'login');

        // Log IP address (lightweight, no external API)
        $this->fraudService->logIpAddress($user, $currentIP);

        // Check for suspicious IP changes (different country/city)
        $previousIPs = $user->ipAddresses()->latest()->take(5)->get();
        if ($previousIPs->count() > 0) {
            $lastIP = $previousIPs->first();
            if ($lastIP->country !== $deviceData['country']) {
                Log::warning('🌍 LOCATION CHANGE DETECTED', [
                    'user_id' => $user->id,
                    'previous_country' => $lastIP->country,
                    'new_country' => $deviceData['country'],
                    'previous_ip' => $lastIP->ip_address,
                    'new_ip' => $currentIP,
                ]);
            }
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($throttleKey);

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Login user
        auth()->login($user);

        Log::info('✅ LOGIN SUCCESSFUL', [
            'user_id' => $user->id,
            'user_name' => $user->full_name,
            'ip' => $currentIP,
        ]);

        // Check if 2FA is enabled
        if ($user->google2fa_enabled) {
            session(['2fa_user_id' => $user->id]);
            auth()->logout();
            return redirect()->route('2fa.verify');
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Log::info('👋 USER LOGOUT', [
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->full_name,
        ]);

        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
