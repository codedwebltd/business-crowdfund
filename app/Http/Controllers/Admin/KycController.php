<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\KycVerification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KycController extends Controller
{
    /**
     * Display KYC verifications
     */
    public function index()
    {
        $kycVerifications = KycVerification::with(['user', 'reviewer'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $stats = [
            'pending' => KycVerification::where('status', 'PENDING')->count(),
            'approved' => KycVerification::where('status', 'APPROVED')->count(),
            'rejected' => KycVerification::where('status', 'REJECTED')->count(),
            'total' => KycVerification::count(),
        ];

        $settings = GlobalSetting::first();

        return Inertia::render('Admin/KycVerifications', [
            'kycVerifications' => $kycVerifications,
            'stats' => $stats,
            'settings' => $settings,
        ]);
    }

    /**
     * Approve KYC verification
     */
    public function approve(Request $request, $id)
    {
        $kyc = KycVerification::with('user')->findOrFail($id);

        if ($kyc->status !== 'PENDING') {
            return back()->withErrors(['message' => 'This KYC verification has already been processed.']);
        }

        $kyc->update([
            'status' => 'APPROVED',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        // Update user's kyc_verified_at timestamp
        $kyc->user->update([
            'kyc_verified_at' => now(),
        ]);

        return back()->with('success', 'KYC verification approved successfully!');
    }

    /**
     * Reject KYC verification
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $kyc = KycVerification::with('user')->findOrFail($id);

        if ($kyc->status !== 'PENDING') {
            return back()->withErrors(['message' => 'This KYC verification has already been processed.']);
        }

        $kyc->update([
            'status' => 'REJECTED',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'rejection_reason' => $request->input('reason'),
        ]);

        // Clear user's kyc_verified_at if they had one
        $kyc->user->update([
            'kyc_verified_at' => null,
        ]);

        return back()->with('success', 'KYC verification rejected. User has been notified.');
    }
}
