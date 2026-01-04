<?php

namespace App\Jobs;

use App\Models\GlobalSetting;
use App\Services\AITaskGeneratorService;
use App\Helpers\CountryHelper;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateReviewTasksJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 2;

    protected $count;

    public function __construct($count = 5)
    {
        $this->count = $count;
        $this->onQueue('default');
    }

    public function handle(AITaskGeneratorService $taskGenerator): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        Log::info("⭐ Starting review task generation", ['count' => $this->count]);

        $settings = GlobalSetting::first();
        $countryCode = $settings->country_of_operation ?? 'NGA';
        $country = CountryHelper::getByAlpha3($countryCode);
        $countryName = $country['name'] ?? 'Nigeria';

        try {
            $generated = $taskGenerator->generateProductReviews($countryName, $this->count);

            Log::info("✅ Review tasks generated", [
                'requested' => $this->count,
                'generated' => $generated,
                'batch_id' => $this->batch()?->id,
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Review task generation failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateReviewTasksJob failed", [
            'count' => $this->count,
            'error' => $exception->getMessage()
        ]);
    }
}
