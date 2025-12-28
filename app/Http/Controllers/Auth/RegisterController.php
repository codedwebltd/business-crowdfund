<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailNotificationService;
use App\Services\FraudDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class RegisterController extends Controller
{
    protected EmailNotificationService $emailService;
    protected FraudDetectionService $fraudService;

    public function __construct(EmailNotificationService $emailService, FraudDetectionService $fraudService)
    {
        $this->emailService = $emailService;
        $this->fraudService = $fraudService;
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm(Request $request)
    {
        $referralCode = $request->query('ref');
        $settings = \App\Models\GlobalSetting::first();

        return Inertia::render('Auth/Register', [
            'referralCode' => $referralCode,
            'smsEnabled' => $settings->sms_notifications_enabled ?? false,
            'appName' => $settings->app_name ?? config('app.name'),
            'registrationEnabled' => $settings->new_registrations_enabled ?? true,
            'settings' => $settings,
        ]);
    }

    /**
     * Handle registration (3-step flow)
     * Step 1: Send OTP (phone/email only)
     * Step 3: Complete registration (full details + user_id)
     */
    public function register(Request $request)
    {
        // Step 1: Initial registration - Send OTP
        if (!$request->has('user_id')) {
            $validated = $request->validate([
                'phone_number' => ['nullable', 'string', 'regex:/^\+?[1-9]\d{1,14}$/', 'unique:users,phone_number'],
                'email' => ['nullable', 'email:rfc,dns', 'unique:users,email'],
                'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
            ], [
                'phone_number.regex' => 'Please enter a valid phone number with country code.',
                'email.email' => 'Please enter a valid email address.',
                'phone_number.unique' => 'This phone number is already registered.',
                'email.unique' => 'This email address is already registered.',
                'referral_code.exists' => 'Invalid referral code.',
            ]);

            // Ensure at least phone OR email is provided
            if (empty($validated['phone_number']) && empty($validated['email'])) {
                return back()->withErrors(['message' => 'Please provide either phone number or email address.']);
            }

            // Additional email validation - check if it's a real domain
            if (!empty($validated['email'])) {
                $emailParts = explode('@', $validated['email']);
                if (count($emailParts) === 2) {
                    $domain = $emailParts[1];
                    // Check if domain has MX records (real email provider)
                    if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
                        return back()->withErrors(['email' => 'Please use a valid email address from a real email provider.']);
                    }
                }
            }

            // Find referrer if referral code provided
            $referrer = null;
            if (!empty($validated['referral_code'])) {
                $referrer = User::where('referral_code', $validated['referral_code'])->first();
            }

            // Create temporary user for OTP verification
            $user = User::create([
                'phone_number' => $validated['phone_number'] ?? null,
                'email' => $validated['email'] ?? null,
                'full_name' => 'Pending', // Temporary value
                'referred_by_id' => $referrer?->id,
                'status' => 'UNVERIFIED',
               
            ]);

            // Generate and send OTP
            $otp = $user->generateOTP();
            $otpSent = $this->emailService->sendOTP($user, $otp);

            $settings = \App\Models\GlobalSetting::first();

            return Inertia::render('Auth/Register', [
                'referralCode' => $validated['referral_code'] ?? null,
                'smsEnabled' => $settings->sms_notifications_enabled ?? false,
                'appName' => $settings->app_name ?? config('app.name'),
                'settings' => \App\Models\GlobalSetting::get(),
                'user_id' => $user->id,
            ]);
        }

        // Step 3: Complete registration with full details
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'phone_number' => ['nullable', 'string', 'unique:users,phone_number,' . $request->user_id],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email,' . $request->user_id],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'password' => ['required', 'confirmed', Password::defaults()],
             
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
        ]);

        // Add custom age validation
        $validator->after(function ($validator) use ($request) {
            if ($request->date_of_birth) {
                $dob = \Carbon\Carbon::parse($request->date_of_birth);
                if ($dob->age < 18) {
                    $validator->errors()->add('date_of_birth', 'You must be at least 18 years old to register.');
                }
            }
        });

        if ($validator->fails()) {
            $user = User::find($request->user_id);
            $settings = \App\Models\GlobalSetting::first();

            return Inertia::render('Auth/Register', [
                'referralCode' => $user && $user->referred_by_id ? $user->referrer->referral_code : null,
                'smsEnabled' => $settings->sms_notifications_enabled ?? false,
                'appName' => $settings->app_name ?? config('app.name'),
                'settings' => \App\Models\GlobalSetting::get(),
                'user_id' => $request->user_id,
                'errors' => $validator->errors()->getMessages(),
            ]);
        }

        $validated = $validator->validated();

        // Get the existing user
        $user = User::findOrFail($validated['user_id']);

        // Update user with full details
        $user->update([
            'phone_number' => $validated['phone_number'] ?? $user->phone_number,
            'full_name' => $validated['full_name'],
            'email' => $validated['email'] ?? $user->email,
            'date_of_birth' => $validated['date_of_birth'],
            'password' => Hash::make($validated['password']),
            'status' => 'PENDING', // Not activated until payment
            'password_confirmation' => $validated['password'],
        ]);

        // Send simple welcome email
        try {
            $this->emailService->sendSimpleWelcome($user);
        } catch (\Exception $e) {
            logger()->error("Failed to send welcome email: " . $e->getMessage());
        }

        // Login user
        auth()->login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($user->isOTPValid($validated['otp_code'])) {
            // Mark phone as verified
            $user->update([
                'phone_verified_at' => now(),
            ]);

            // Clear OTP
            $user->clearOTP();

            $settings = \App\Models\GlobalSetting::first();

            // Return to step 3 (don't login yet, need full registration)
            return Inertia::render('Auth/Register', [
                'referralCode' => $user->referred_by_id ? $user->referrer->referral_code : null,
                'smsEnabled' => $settings->sms_notifications_enabled ?? false,
                'appName' => $settings->app_name ?? config('app.name'),
                'settings' => \App\Models\GlobalSetting::get(),
                'user_id' => $user->id,
            ]);
        }

        return back()->withErrors(['otp_code' => 'Invalid or expired OTP code. Please try again.']);
    }

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Generate new OTP
        $otp = $user->generateOTP();

        // Send OTP
        $otpSent = $this->emailService->sendOTP($user, $otp);

        $settings = \App\Models\GlobalSetting::first();

        return Inertia::render('Auth/Register', [
            'referralCode' => $user->referred_by_id ? $user->referrer->referral_code : null,
            'smsEnabled' => $settings->sms_notifications_enabled ?? false,
            'appName' => $settings->app_name ?? config('app.name'),
            'settings' => \App\Models\GlobalSetting::get(),
            'user_id' => $user->id,
        ]);
    }
}
