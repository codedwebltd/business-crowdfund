<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Google2FAService;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct(Google2FAService $google2fa)
    {
        $this->google2fa = $google2fa;
    }

    public function show()
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/TwoFactorChallenge');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $userId = session('2fa_user_id');
        if (!$userId) {
            return back()->withErrors(['code' => 'Session expired. Please login again.']);
        }

        $user = User::find($userId);
        if (!$user || !$user->google2fa_enabled) {
            return back()->withErrors(['code' => 'Invalid session.']);
        }

        if (!$this->google2fa->verify($user->google2fa_secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code']);
        }

        session()->forget('2fa_user_id');
        session(['2fa_verified' => true]);

        auth()->login($user);

        return redirect()->intended('/dashboard');
    }

    public function verifyBackup(Request $request)
    {
        $request->validate(['backup_code' => 'required|string']);

        $userId = session('2fa_user_id');
        if (!$userId) {
            return back()->withErrors(['backup_code' => 'Session expired. Please login again.']);
        }

        $user = User::find($userId);
        if (!$user || !$user->google2fa_enabled || !$user->backup_codes) {
            return back()->withErrors(['backup_code' => 'Invalid session.']);
        }

        $backupCodes = $user->backup_codes;
        $codeIndex = $this->google2fa->verifyBackupCode($backupCodes, $request->backup_code);

        if ($codeIndex === false) {
            return back()->withErrors(['backup_code' => 'Invalid or already used backup code']);
        }

        // Mark code as used
        $backupCodes[$codeIndex]['used'] = true;
        $user->update(['backup_codes' => $backupCodes]);

        session()->forget('2fa_user_id');
        session(['2fa_verified' => true]);

        auth()->login($user);

        return redirect()->intended('/dashboard');
    }
}
