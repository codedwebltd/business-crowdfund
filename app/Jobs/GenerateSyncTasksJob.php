<?php

namespace App\Jobs;

use App\Services\AITaskGeneratorService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateSyncTasksJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 2;

    protected $count;

    public function __construct($count = 3)
    {
        $this->count = $count;
        $this->onQueue('default');
    }

    public function handle(AITaskGeneratorService $taskGenerator): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        Log::info("🔄 Starting sync task generation", ['count' => $this->count]);

        try {
            $generated = $taskGenerator->generateSyncTasks($this->count);

            Log::info("✅ Sync tasks generated", [
                'requested' => $this->count,
                'generated' => $generated,
                'batch_id' => $this->batch()?->id,
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Sync task generation failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateSyncTasksJob failed", [
            'count' => $this->count,
            'error' => $exception->getMessage()
        ]);
    }
}
