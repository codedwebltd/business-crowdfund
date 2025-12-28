<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Google2FAService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    protected $google2fa;

    public function __construct(Google2FAService $google2fa)
    {
        $this->google2fa = $google2fa;
    }

    public function index()
    {
        $backupCodes = session('backupCodes');

        // Clear backup codes after first display
        if ($backupCodes) {
            session()->forget('backupCodes');
        }

        $settings = \App\Models\GlobalSetting::first();
        $user = auth()->user();

        // Safety: Handle if user not found
        if (!$user) {
            return Inertia::render('User/Settings', [
                'user' => null,
                'twoFactorEnabled' => false,
                'qrCode' => null,
                'secret' => null,
                'backupCodes' => null,
                'settings' => $settings,
                'notificationSettings' => [
                    'email' => true,
                    'task_reminders' => true,
                    'referral_updates' => true,
                    'token_updates' => true,
                ],
            ]);
        }

        // Get notification preferences
        $notificationSettings = $user->notification_preferences ?? [
            'email' => true,
            'task_reminders' => true,
            'referral_updates' => true,
            'token_updates' => true,
        ];

        return Inertia::render('User/Settings', [
            'user' => $user->load('plan'),
            'twoFactorEnabled' => $user->google2fa_enabled ?? false,
            'qrCode' => session('qrCode'),
            'secret' => session('secret'),
            'backupCodes' => $backupCodes,
            'settings' => $settings,
            'notificationSettings' => $notificationSettings,
        ]);
    }

    public function enable2FA(Request $request)
    {
        $user = auth()->user();

        if ($user->google2fa_enabled) {
            return back()->with('error', '2FA is already enabled');
        }

        $secret = $this->google2fa->generateSecretKey();
        $qrCode = $this->google2fa->generateQRCode($user->email ?? $user->phone_number, $secret);

        session([
            'qrCode' => $qrCode,
            'secret' => $secret,
        ]);

        return redirect()->route('settings');
    }

    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
            'secret' => 'required',
        ]);

        $user = auth()->user();

        if (!$this->google2fa->verify($request->secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code']);
        }

        $backupCodes = $this->google2fa->generateBackupCodes();
        $encryptedCodes = $this->google2fa->encryptBackupCodes($backupCodes);

        $user->update([
            'google2fa_secret' => $request->secret,
            'google2fa_enabled' => true,
            'backup_codes' => $encryptedCodes,
        ]);

        session()->forget(['qrCode', 'secret']);
        session(['backupCodes' => $backupCodes]);

        return redirect()->route('settings')->with('success', '2FA enabled successfully');
    }

    public function disable2FA(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = auth()->user();

        if (!$user->google2fa_enabled) {
            return back()->withErrors(['code' => '2FA is not enabled']);
        }

        if (!$this->google2fa->verify($user->google2fa_secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code']);
        }

        $user->update([
            'google2fa_secret' => null,
            'google2fa_enabled' => false,
            'backup_codes' => null,
        ]);

        session()->forget(['qrCode', 'secret', 'backupCodes']);

        return back()->with('success', '2FA disabled successfully');
    }

    public function cancel2FA()
    {
        session()->forget(['qrCode', 'secret', 'backupCodes']);
        return back();
    }

    public function regenerateBackupCodes(Request $request)
    {
        $request->validate(['code' => 'required']);

        $user = auth()->user();

        if (!$user->google2fa_enabled) {
            return back()->with('error', '2FA is not enabled');
        }

        if (!$this->google2fa->verify($user->google2fa_secret, $request->code)) {
            return back()->with('error', 'Invalid verification code');
        }

        $backupCodes = $this->google2fa->generateBackupCodes();
        $encryptedCodes = $this->google2fa->encryptBackupCodes($backupCodes);

        $user->update(['backup_codes' => $encryptedCodes]);

        return back()->with([
            'success' => 'Backup codes regenerated',
            'backupCodes' => $backupCodes,
        ]);
    }

    public function updatePaymentMethods(Request $request)
    {
        $request->validate([
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|digits:10',
            'account_name' => 'nullable|string|max:255',
            'wallet_address' => 'nullable|string|max:255',
            'coin_name' => 'nullable|string|in:USDT',
            'coin_network' => 'nullable|string|in:TRC20',
            'two_factor_code' => 'nullable|digits:6',
        ]);

        $user = auth()->user();

        // Verify 2FA code if 2FA is enabled
        if ($user->google2fa_enabled) {
            if (!$request->two_factor_code) {
                return back()->withErrors(['two_factor_code' => '2FA verification code is required']);
            }

            if (!$this->google2fa->verify($user->google2fa_secret, $request->two_factor_code)) {
                return back()->withErrors(['two_factor_code' => 'Invalid verification code']);
            }
        }

        // Build wallet_details JSON structure
        $walletDetails = null;
        if ($request->wallet_address && $request->coin_name && $request->coin_network) {
            $walletDetails = [
                'wallet_address' => $request->wallet_address,
                'coin_name' => $request->coin_name,
                'coin_network' => $request->coin_network,
            ];
        }

        $user->update([
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'wallet_details' => $walletDetails,
        ]);

        return back()->with('success', 'Payment methods updated successfully');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully');
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email' => 'required|boolean',
            'task_reminders' => 'required|boolean',
            'referral_updates' => 'required|boolean',
            'token_updates' => 'required|boolean',
        ]);

        auth()->user()->update([
            'notification_preferences' => $request->only(['email', 'task_reminders', 'referral_updates', 'token_updates']),
        ]);

        return back()->with('success', 'Notification preferences saved');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
            'two_factor_code' => 'nullable|digits:6',
        ]);

        $user = auth()->user();

        // Verify 2FA code if 2FA is enabled
        if ($user->google2fa_enabled) {
            if (!$request->two_factor_code) {
                return back()->withErrors(['two_factor_code' => '2FA verification code is required']);
            }

            if (!$this->google2fa->verify($user->google2fa_secret, $request->two_factor_code)) {
                return back()->withErrors(['two_factor_code' => 'Invalid verification code']);
            }
        }

        $user->update([
            'password' => bcrypt($request->new_password),
        ]);

        return back()->with('success', 'Password changed successfully');
    }

    public function deleteAccount(Request $request)
    {
        $user = auth()->user();

        // Log out the user
        auth()->logout();

        // Soft delete or hard delete based on your preference
        $user->delete();

        return redirect('/')->with('success', 'Account deleted successfully');
    }
}
