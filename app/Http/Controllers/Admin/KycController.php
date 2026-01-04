<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\KycVerification;
use App\Services\FileUploadService;
use App\Services\NotificationService;
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

        // Send notification to user
        $notificationService = app(NotificationService::class);
        $notificationService->send($kyc->user, 'kyc_approved', [
            'kyc_id' => $kyc->id,
            'approved_at' => now()->toDateTimeString(),
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

        // Send notification to user
        $notificationService = app(NotificationService::class);
        $notificationService->send($kyc->user, 'kyc_rejected', [
            'kyc_id' => $kyc->id,
            'rejection_reason' => $request->input('reason'),
            'rejected_at' => now()->toDateTimeString(),
        ]);

        return back()->with('success', 'KYC verification rejected. User has been notified.');
    }

    /**
     * Delete KYC document from storage
     */
    public function deleteDocument(Request $request, $id, FileUploadService $fileUploadService)
    {
        $request->validate([
            'document_type' => 'required|in:nin,utility_bill,selfie',
        ]);

        $kyc = KycVerification::findOrFail($id);
        $docType = $request->input('document_type');
        $urlField = $docType . '_url';

        if (!$kyc->$urlField) {
            return back()->withErrors(['message' => 'Document not found.']);
        }

        try {
            // Extract file path from URL
            $url = $kyc->$urlField;
            $bucketName = config('filesystems.backblaze.bucket_name', 'westernkits');

            // Parse the file path from the URL
            if (str_contains($url, 'file/' . $bucketName . '/')) {
                $filePath = explode('file/' . $bucketName . '/', $url)[1];
            } else {
                // If URL doesn't contain expected pattern, try alternative parsing
                $filePath = basename(parse_url($url, PHP_URL_PATH));
            }

            // Delete from Backblaze
            $deleted = $fileUploadService->deleteFile($filePath);

            if ($deleted) {
                // Remove URL from database
                $kyc->update([$urlField => null]);
                return back()->with('success', 'Document deleted successfully from storage.');
            }

            return back()->withErrors(['message' => 'Failed to delete file from storage.']);

        } catch (\Exception $e) {
            \Log::error('Failed to delete KYC document', [
                'kyc_id' => $id,
                'document_type' => $docType,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['message' => 'Failed to delete document: ' . $e->getMessage()]);
        }
    }
}
