<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CommandControlController extends Controller
{
    public function index()
    {
        $settings = GlobalSetting::first();
        
        // Get batch jobs status
        $batches = DB::table('job_batches')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Get recent failed jobs
        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->limit(10)
            ->get();

        // Get queue jobs count
        $pendingJobs = DB::table('jobs')->count();

        return Inertia::render('Admin/Commands', [
            'settings' => $settings,
            'batches' => $batches,
            'failedJobs' => $failedJobs,
            'pendingJobs' => $pendingJobs,
        ]);
    }

    public function execute(Request $request)
    {
        $request->validate([
            'command' => 'required|string',
            'arguments' => 'sometimes|array'
        ]);

        $command = $request->command;
        $arguments = $request->arguments ?? [];

        try {
            // Security: Only allow specific whitelisted commands
            $allowedCommands = [
                'tasks:assign-daily',
                'tasks:generate-templates',
                'tasks:mature-earnings',
                'commissions:disburse',
                'liquidity:calculate-burn-rate',
                'purge:unpaid-accounts',
                'cleanup:soft-deletes',
                'queue:work',
                'queue:restart',
                'cache:clear',
                'config:clear',
                'route:clear',
                'view:clear',
            ];

            if (!in_array($command, $allowedCommands)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Command not allowed'
                ], 403);
            }

            // Execute command
            Artisan::call($command, $arguments);
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'output' => $output,
                'message' => 'Command executed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBatchStatus($batchId)
    {
        $batch = Bus::findBatch($batchId);

        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'batch' => [
                'id' => $batch->id,
                'name' => $batch->name,
                'total_jobs' => $batch->totalJobs,
                'pending_jobs' => $batch->pendingJobs,
                'failed_jobs' => $batch->failedJobs,
                'processed_jobs' => $batch->processedJobs(),
                'progress' => $batch->progress(),
                'finished' => $batch->finished(),
                'cancelled' => $batch->cancelled(),
                'created_at' => $batch->createdAt,
                'finished_at' => $batch->finishedAt,
            ]
        ]);
    }

    public function retryFailedJob($id)
    {
        try {
            Artisan::call('queue:retry', ['id' => $id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Job queued for retry'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function clearFailedJobs()
    {
        try {
            Artisan::call('queue:flush');

            return response()->json([
                'success' => true,
                'message' => 'All failed jobs cleared'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getLaravelLog()
    {
        try {
            $logPath = storage_path('logs/laravel.log');

            if (!file_exists($logPath)) {
                return response()->json([
                    'success' => true,
                    'content' => 'No log file found',
                    'size' => 0
                ]);
            }

            $fileSize = filesize($logPath);

            // Read last 500KB of log file
            $maxSize = 500 * 1024;
            if ($fileSize > $maxSize) {
                $file = fopen($logPath, 'r');
                fseek($file, $fileSize - $maxSize);
                $content = fread($file, $maxSize);
                fclose($file);
            } else {
                $content = file_get_contents($logPath);
            }

            return response()->json([
                'success' => true,
                'content' => $content,
                'size' => $fileSize,
                'size_readable' => $this->formatBytes($fileSize)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function clearLaravelLog()
    {
        try {
            $logPath = storage_path('logs/laravel.log');

            if (file_exists($logPath)) {
                file_put_contents($logPath, '');
            }

            return response()->json([
                'success' => true,
                'message' => 'Laravel log cleared'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function clearBatches()
    {
        try {
            // Clear batch jobs
            DB::table('job_batches')->truncate();

            // Clear pending jobs from queue
            DB::table('jobs')->truncate();

            return response()->json([
                'success' => true,
                'message' => 'All batch jobs and pending jobs cleared'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
