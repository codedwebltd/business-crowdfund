<?php

namespace App\Jobs;

use App\Models\GlobalSetting;
use App\Models\TaskTemplate;
use App\Services\AITaskGeneratorService;
use App\Helpers\CountryHelper;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateVideoTasksJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 2;

    protected $count;

    public function __construct($count = 6)
    {
        $this->count = $count;
        $this->onQueue('default');
    }

    public function handle(AITaskGeneratorService $taskGenerator): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        Log::info("🎬 Starting video task generation", ['count' => $this->count]);

        $settings = GlobalSetting::first();
        $countryCode = $settings->country_of_operation ?? 'NGA';

        try {
            $generated = $taskGenerator->generateVideoTasks($countryCode, $this->count);

            Log::info("✅ Video tasks generated", [
                'requested' => $this->count,
                'generated' => $generated,
                'batch_id' => $this->batch()?->id,
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Video task generation failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateVideoTasksJob failed", [
            'count' => $this->count,
            'error' => $exception->getMessage()
        ]);
    }
}
