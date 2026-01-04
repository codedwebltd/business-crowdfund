<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Google Cloud Vision API Connection...\n\n";

try {
    $vision = app(\App\Services\VisionService::class);

    // Test with a random image from internet
    $testUrl = 'https://picsum.photos/seed/kyc-test/400/600';

    echo "Analyzing image: $testUrl\n";
    echo "Document type: NIN\n\n";

    $result = $vision->analyzeDocument($testUrl, 'nin');

    echo "Results:\n";
    echo "--------\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

    if ($result['success']) {
        echo "✓ Vision API Connected Successfully!\n";
        echo "Confidence: " . ($result['confidence'] ?? 0) . "%\n";
        echo "Passed: " . (($result['passed'] ?? false) ? 'Yes' : 'No') . "\n";
    } else {
        echo "✗ Analysis failed: " . ($result['message'] ?? 'Unknown error') . "\n";
    }

} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
