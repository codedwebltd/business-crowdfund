<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\VerifyKycDocuments;
use App\Models\GlobalSetting;
use App\Models\KycVerification;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class KycController extends Controller
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Upload KYC document
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|in:nin,utility_bill,selfie'
        ]);

        try {
            $user = auth()->user();
            $file = $request->file('file');
            $type = $request->input('type');

            // Upload to Backblaze B2
            $result = $this->fileUploadService->uploadFile(
                $file,
                'kyc/' . $type,
                $user->id
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'url' => $result['url'],
                    'path' => $result['path']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Upload failed. Please try again.'
            ], 500);

        } catch (\Exception $e) {
            \Log::error('KYC upload failed', [
                'user_id' => auth()->id(),
                'type' => $request->input('type'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit KYC verification
     */
    public function submit(Request $request)
    {
        $user = auth()->user();
        $settings = GlobalSetting::first();
        $kycRequirements = $settings->kyc_requirements ?? [];

        // Build validation rules based on admin settings
        $rules = [];

        // Date of birth (always required if not set)
        if (!$user->date_of_birth) {
            $rules['date_of_birth'] = 'required|date|before:-18 years';
        }

        // NIN
        if ($kycRequirements['nin_required'] ?? false) {
            $rules['nin'] = 'required|string|size:11';
            $rules['nin_url'] = 'required|url';
        }

        // BVN
        if ($kycRequirements['bvn_required'] ?? false) {
            $rules['bvn'] = 'required|string|size:11';
        }

        // Utility Bill
        if ($kycRequirements['utility_bill_required'] ?? false) {
            $rules['utility_bill_url'] = 'required|url';
        }

        // Selfie
        if ($kycRequirements['selfie_required'] ?? false) {
            $rules['selfie_url'] = 'required|url';
        }

        $validated = $request->validate($rules);

        try {
            // Update user basic info
            $userUpdateData = [];
            if (isset($validated['date_of_birth']) && !$user->date_of_birth) {
                $userUpdateData['date_of_birth'] = $validated['date_of_birth'];
            }
            if (isset($validated['nin'])) {
                $userUpdateData['nin'] = $validated['nin'];
            }
            if (isset($validated['bvn'])) {
                $userUpdateData['bvn'] = $validated['bvn'];
            }

            if (!empty($userUpdateData)) {
                $user->update($userUpdateData);
            }

            // Create or update KYC verification record
            $kycData = [
                'user_id' => $user->id,
                'nin_url' => $validated['nin_url'] ?? null,
                'utility_bill_url' => $validated['utility_bill_url'] ?? null,
                'selfie_url' => $validated['selfie_url'] ?? null,
                'status' => 'PENDING',
                'submitted_at' => now(),
            ];

            // Check if user already has a pending/rejected KYC
            $existingKyc = $user->latestKyc;

            if ($existingKyc && in_array($existingKyc->status, ['PENDING', 'REJECTED'])) {
                // Delete old files from Backblaze if they're being replaced
                if (isset($validated['nin_url']) && $existingKyc->nin_url && $existingKyc->nin_url !== $validated['nin_url']) {
                    $this->fileUploadService->deleteFile($existingKyc->nin_url);
                }
                if (isset($validated['utility_bill_url']) && $existingKyc->utility_bill_url && $existingKyc->utility_bill_url !== $validated['utility_bill_url']) {
                    $this->fileUploadService->deleteFile($existingKyc->utility_bill_url);
                }
                if (isset($validated['selfie_url']) && $existingKyc->selfie_url && $existingKyc->selfie_url !== $validated['selfie_url']) {
                    $this->fileUploadService->deleteFile($existingKyc->selfie_url);
                }

                // Update existing record
                $existingKyc->update($kycData);
                $kyc = $existingKyc;
            } else {
                // Create new
                $kyc = KycVerification::create($kycData);
            }

            // Dispatch job for background auto-verification
            VerifyKycDocuments::dispatch($kyc);

            return back()->with('success', 'KYC verification submitted successfully! We are processing your documents and will notify you shortly.');

        } catch (\Exception $e) {
            \Log::error('KYC submission failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['submit' => 'Submission failed. Please try again.']);
        }
    }

    /**
     * Get KYC status
     */
    public function status()
    {
        $user = auth()->user()->load('latestKyc');
        $settings = GlobalSetting::first();

        return response()->json([
            'kyc' => $user->latestKyc,
            'kyc_requirements' => $settings->kyc_requirements ?? [],
            'kyc_threshold' => $settings->kyc_withdrawal_threshold ?? 50000,
            'enable_kyc_on_first_withdrawal' => $settings->enable_kyc_on_first_withdrawal ?? false,
        ]);
    }
}
