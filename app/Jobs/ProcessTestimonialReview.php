<?php

namespace App\Jobs;

use App\Models\Testimonial;
use App\Services\GroqService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessTestimonialReview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $testimonial;

    /**
     * Create a new job instance.
     */
    public function __construct(Testimonial $testimonial)
    {
        $this->testimonial = $testimonial;
    }

    /**
     * Execute the job.
     */
    public function handle(GroqService $groqService, NotificationService $notificationService): void
    {
        try {
            Log::info('Processing testimonial with AI', ['testimonial_id' => $this->testimonial->id]);

            // Analyze testimonial with Groq
            $analysis = $groqService->reviewTestimonial($this->testimonial->message);

            if (!$analysis) {
                Log::error('Groq analysis failed for testimonial', ['testimonial_id' => $this->testimonial->id]);
                $this->sendManualReviewNotification($notificationService, 'AI analysis failed - needs manual review');
                return;
            }

            // Update testimonial with AI analysis
            $this->testimonial->update([
                'ai_analysis' => $analysis,
                'ai_corrected_message' => $analysis['corrected_message'] ?? null,
                'auto_approved' => $analysis['approved'] ?? false,
                'trash_testimonial' => $analysis['trash_testimonial'] ?? false,
                'negative_testimonial' => $analysis['negative_testimonial'] ?? false,
                'complaint_testimonial' => $analysis['complaint_testimonial'] ?? false,
                'ai_processed_at' => now(),
            ]);

            // Auto-approve if AI says it's good
            if ($analysis['approved'] === true) {
                $this->testimonial->update([
                    'status' => 'APPROVED',
                    'reviewed_at' => now(),
                    'admin_notes' => 'Auto-approved by AI: ' . ($analysis['reason'] ?? 'Positive testimonial')
                ]);

                $this->sendApprovalNotification($notificationService);
                Log::info('Testimonial auto-approved', ['testimonial_id' => $this->testimonial->id]);
            } else {
                // Send manual review notification
                $reason = $this->getReviewReason($analysis);
                $this->sendManualReviewNotification($notificationService, $reason);
                Log::info('Testimonial needs manual review', [
                    'testimonial_id' => $this->testimonial->id,
                    'reason' => $reason
                ]);
            }

        } catch (Exception $e) {
            Log::error('Error processing testimonial review', [
                'testimonial_id' => $this->testimonial->id,
                'error' => $e->getMessage()
            ]);
            $this->sendManualReviewNotification($notificationService, 'Error during AI processing - needs manual review');
        }
    }

    /**
     * Send approval notification to user using NotificationService
     */
    protected function sendApprovalNotification(NotificationService $notificationService)
    {
        $notificationService->send(
            $this->testimonial->user,
            'testimonial_approved',
            [
                'testimonial_id' => $this->testimonial->id,
                'auto_approved' => true,
            ]
        );
    }

    /**
     * Send manual review notification to user using NotificationService
     */
    protected function sendManualReviewNotification(NotificationService $notificationService, $reason)
    {
        $notificationService->send(
            $this->testimonial->user,
            'testimonial_review',
            [
                'testimonial_id' => $this->testimonial->id,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Get human-readable review reason
     */
    protected function getReviewReason($analysis)
    {
        if ($analysis['trash_testimonial'] ?? false) {
            return 'Testimonial appears to be random text or nonsense';
        }
        if ($analysis['negative_testimonial'] ?? false) {
            return 'Testimonial contains negative feedback about the platform';
        }
        if ($analysis['complaint_testimonial'] ?? false) {
            return 'Testimonial contains a complaint or issue';
        }
        return $analysis['reason'] ?? 'Needs manual review';
    }
}
