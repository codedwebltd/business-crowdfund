<?php

namespace App\Jobs;

use App\Models\KycVerification;
use App\Services\NotificationService;
use App\Services\VisionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyKycDocuments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300; // 5 minutes max

    protected KycVerification $kyc;

    public function __construct(KycVerification $kyc)
    {
        $this->kyc = $kyc;
    }

    public function handle(VisionService $visionService, NotificationService $notificationService): void
    {
        try {
            Log::info('Starting KYC auto-verification', [
                'kyc_id' => $this->kyc->id,
                'user_id' => $this->kyc->user_id
            ]);

            $autoVerificationResults = [];
            $documentsToAnalyze = [];

            // Gather documents to analyze
            if ($this->kyc->nin_url) {
                $documentsToAnalyze['nin'] = $this->kyc->nin_url;
            }
            if ($this->kyc->utility_bill_url) {
                $documentsToAnalyze['utility_bill'] = $this->kyc->utility_bill_url;
            }
            if ($this->kyc->selfie_url) {
                $documentsToAnalyze['selfie'] = $this->kyc->selfie_url;
            }

            // Analyze each document
            foreach ($documentsToAnalyze as $type => $url) {
                $analysis = $visionService->analyzeDocument($url, $type);
                $autoVerificationResults[$type] = $analysis;

                Log::info("Analyzed KYC document: $type", [
                    'kyc_id' => $this->kyc->id,
                    'confidence' => $analysis['confidence'] ?? 0,
                    'passed' => $analysis['passed'] ?? false
                ]);
            }

            // Store verification data
            $this->kyc->update([
                'verification_data' => $autoVerificationResults,
            ]);

            // Check if should auto-approve
            if ($visionService->shouldAutoApprove($autoVerificationResults)) {
                $this->approveKyc($autoVerificationResults, $notificationService);
            } else {
                $this->pendingReview($autoVerificationResults, $notificationService);
            }

        } catch (\Exception $e) {
            Log::error('KYC auto-verification failed', [
                'kyc_id' => $this->kyc->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Mark as pending for manual review
            $this->kyc->update([
                'verification_data' => [
                    'error' => $e->getMessage(),
                    'auto_verification_failed' => true
                ]
            ]);

            throw $e; // Let queue retry
        }
    }

    protected function approveKyc(array $results, NotificationService $notificationService): void
    {
        $this->kyc->update([
            'status' => 'APPROVED',
            'reviewed_at' => now(),
            'reviewed_by' => null,
            'auto_verified' => true,
        ]);

        $this->kyc->user->update(['kyc_verified_at' => now()]);

        $notificationService->send(
            $this->kyc->user,
            'kyc_approved',
            [
                'kyc_id' => $this->kyc->id,
                'auto_verified' => true,
                'message' => 'Your KYC verification has been automatically approved!',
                'confidence_scores' => array_map(fn($r) => $r['confidence'] ?? 0, $results)
            ]
        );

        Log::info('KYC Auto-Approved', [
            'kyc_id' => $this->kyc->id,
            'user_id' => $this->kyc->user_id
        ]);
    }

    protected function pendingReview(array $results, NotificationService $notificationService): void
    {
        $notificationService->send(
            $this->kyc->user,
            'kyc_pending_review',
            [
                'kyc_id' => $this->kyc->id,
                'message' => 'Your KYC documents are under review. We will notify you within 24-48 hours.',
                'submitted_at' => $this->kyc->submitted_at->toDateTimeString()
            ]
        );

        Log::info('KYC Pending Manual Review', [
            'kyc_id' => $this->kyc->id,
            'user_id' => $this->kyc->user_id,
            'reason' => 'Did not meet auto-approval threshold'
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('KYC verification job failed permanently', [
            'kyc_id' => $this->kyc->id,
            'error' => $exception->getMessage()
        ]);

        $this->kyc->update([
            'verification_data' => [
                'error' => $exception->getMessage(),
                'auto_verification_failed' => true,
                'failed_at' => now()
            ]
        ]);

        // Send notification to user about delayed approval
        try {
            $notificationService = app(NotificationService::class);
            $notificationService->send(
                $this->kyc->user,
                'kyc_pending_review',
                [
                    'kyc_id' => $this->kyc->id,
                    'message' => 'Automatic approval is taking some time due to a massive approval list surge. We will ensure your request is processed shortly.',
                    'submitted_at' => $this->kyc->submitted_at->toDateTimeString(),
                    'estimated_review_time' => '24-48 hours'
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to send KYC notification', [
                'kyc_id' => $this->kyc->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
