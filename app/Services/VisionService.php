<?php

namespace App\Services;

use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Image;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VisionService
{
    protected ?ImageAnnotatorClient $client;
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = config('services.google.enable_kyc_auto_verification', false);

        if ($this->enabled) {
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . config('services.google.cloud_key_file'));
            try {
                $this->client = new ImageAnnotatorClient();
            } catch (\Exception $e) {
                Log::error('Failed to initialize Vision API client', ['error' => $e->getMessage()]);
                $this->enabled = false;
                $this->client = null;
            }
        } else {
            $this->client = null;
        }
    }

    /**
     * Analyze KYC document for auto-verification
     */
    public function analyzeDocument(string $imageUrl, string $documentType): array
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'Vision API auto-verification is disabled',
                'confidence' => 0
            ];
        }

        try {
            // Download image from URL using cURL (allow_url_fopen may be disabled)
            $ch = curl_init($imageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $imageContent = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if (!$imageContent || $httpCode !== 200) {
                throw new \Exception("Failed to download image from URL (HTTP $httpCode)");
            }

            // Prepare Vision API request (v2 API)
            $image = new Image();
            $image->setContent($imageContent);

            $feature = new Feature();
            $feature->setType(Type::TEXT_DETECTION);

            $imageRequest = new AnnotateImageRequest();
            $imageRequest->setImage($image);
            $imageRequest->setFeatures([$feature]);

            $batchRequest = new BatchAnnotateImagesRequest();
            $batchRequest->setRequests([$imageRequest]);

            // Perform text detection
            $response = $this->client->batchAnnotateImages($batchRequest);
            $annotations = $response->getResponses()[0];
            $texts = $annotations->getTextAnnotations();

            if (count($texts) === 0) {
                // For selfies, no text is expected and should PASS
                if ($documentType === 'selfie') {
                    return [
                        'success' => true,
                        'confidence' => 75,
                        'passed' => true,
                        'reasons' => ['Clean selfie image - no text overlay detected'],
                        'keywords_found' => 0,
                        'extracted_text' => ''
                    ];
                }

                // For other documents (NIN, utility bill), no text means failure
                return [
                    'success' => false,
                    'message' => 'No text detected in document',
                    'confidence' => 0,
                    'extracted_text' => ''
                ];
            }

            // Get full text
            $fullText = $texts[0]->getDescription();

            // Analyze based on document type
            $analysis = $this->analyzeByType($fullText, $documentType);

            Log::info('Vision API KYC Analysis', [
                'document_type' => $documentType,
                'extracted_text_length' => strlen($fullText),
                'extracted_text_preview' => substr($fullText, 0, 200),
                'confidence' => $analysis['confidence'],
                'passed' => $analysis['passed'],
                'reasons' => $analysis['reasons'] ?? [],
                'keywords_found' => $analysis['keywords_found'] ?? 0,
                'full_analysis' => $analysis
            ]);

            return array_merge($analysis, [
                'success' => true,
                'extracted_text' => $fullText
            ]);

        } catch (\Exception $e) {
            Log::error('Vision API Analysis Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Analysis failed: ' . $e->getMessage(),
                'confidence' => 0,
                'extracted_text' => ''
            ];
        }
    }

    /**
     * Analyze text based on document type
     */
    protected function analyzeByType(string $text, string $type): array
    {
        $text = strtoupper($text);
        $confidence = 0;
        $passed = false;
        $reasons = [];

        switch ($type) {
            case 'nin':
                // Check for NIN-related keywords
                $ninKeywords = ['NATIONAL', 'IDENTITY', 'NIGERIA', 'NIMC', 'NIN'];
                $foundKeywords = 0;

                foreach ($ninKeywords as $keyword) {
                    if (strpos($text, $keyword) !== false) {
                        $foundKeywords++;
                    }
                }

                // Check for 11-digit number pattern (NIN format)
                if (preg_match('/\b\d{11}\b/', $text)) {
                    $foundKeywords += 2;
                    $reasons[] = 'NIN number detected';
                }

                $confidence = min(100, ($foundKeywords / 7) * 100);
                $passed = $confidence >= 60;

                if (!$passed) {
                    $reasons[] = 'Insufficient NIN document markers';
                }
                break;

            case 'utility_bill':
                // Check for utility bill keywords
                $utilityKeywords = ['BILL', 'ELECTRICITY', 'WATER', 'INVOICE', 'ACCOUNT', 'PAYMENT', 'ADDRESS'];
                $foundKeywords = 0;

                foreach ($utilityKeywords as $keyword) {
                    if (strpos($text, $keyword) !== false) {
                        $foundKeywords++;
                    }
                }

                // Check for date pattern
                if (preg_match('/\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4}/', $text)) {
                    $foundKeywords++;
                    $reasons[] = 'Date detected';
                }

                // Check for amount pattern
                if (preg_match('/₦|NGN|NAIRA|\d+[,\.]\d{2}/', $text)) {
                    $foundKeywords++;
                    $reasons[] = 'Amount detected';
                }

                $confidence = min(100, ($foundKeywords / 9) * 100);
                $passed = $confidence >= 55;

                if (!$passed) {
                    $reasons[] = 'Insufficient utility bill markers';
                }
                break;

            case 'selfie':
                // For selfie, we just check if there's any face-like content
                // Cloud Vision has face detection but text detection won't help much
                // We'll be lenient here and just check image quality
                $wordCount = str_word_count($text);

                if ($wordCount < 10) {
                    $confidence = 70; // Likely a photo, not a document
                    $passed = true;
                    $reasons[] = 'Appears to be a photo';
                } else {
                    $confidence = 40; // Too much text, might be a document photo
                    $passed = false;
                    $reasons[] = 'Too much text detected for a selfie';
                }
                break;

            default:
                $confidence = 0;
                $passed = false;
                $reasons[] = 'Unknown document type';
        }

        return [
            'confidence' => round($confidence, 2),
            'passed' => $passed,
            'reasons' => $reasons,
            'keywords_found' => $foundKeywords ?? 0
        ];
    }

    /**
     * Auto-approve KYC if all documents pass
     */
    public function shouldAutoApprove(array $analyses): bool
    {
        Log::info('🤖 Vision API Auto-Approval Check', [
            'total_documents' => count($analyses),
            'analyses_dump' => $analyses
        ]);

        if (empty($analyses)) {
            Log::warning('⚠️ No analyses to check for auto-approval');
            return false;
        }

        $totalConfidence = 0;
        $count = 0;

        foreach ($analyses as $docType => $analysis) {
            Log::info("📄 Checking document: $docType", [
                'has_passed_key' => isset($analysis['passed']),
                'passed_value' => $analysis['passed'] ?? 'N/A',
                'confidence' => $analysis['confidence'] ?? 'N/A',
                'analysis' => $analysis
            ]);

            // If analysis failed (no 'passed' key) or didn't pass, reject auto-approval
            if (!isset($analysis['passed']) || !$analysis['passed']) {
                Log::warning("❌ Document $docType failed - rejecting auto-approval");
                return false; // One failed = no auto-approval
            }
            $totalConfidence += $analysis['confidence'] ?? 0;
            $count++;
        }

        $averageConfidence = $count > 0 ? $totalConfidence / $count : 0;

        Log::info('✅ All documents passed individual checks', [
            'average_confidence' => $averageConfidence,
            'threshold' => 70,
            'will_auto_approve' => $averageConfidence >= 70
        ]);

        // Auto-approve if average confidence >= 70%
        return $averageConfidence >= 70;
    }

    public function __destruct()
    {
        if ($this->client !== null) {
            try {
                $this->client->close();
            } catch (\Exception $e) {
                // Ignore close errors
            }
        }
    }
}
