<?php

namespace App\Console\Commands;

use App\Services\AITaskGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateTaskTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:generate-templates {--force : Force generation even if threshold not reached}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate task templates using AI when inventory is low';

    protected $taskGenerator;

    public function __construct(AITaskGeneratorService $taskGenerator)
    {
        parent::__construct();
        $this->taskGenerator = $taskGenerator;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🤖 AI Task Template Generator');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $startTime = microtime(true);

        // Check if generation is needed
        if (!$this->option('force') && !$this->taskGenerator->shouldGenerateTasks()) {
            $this->warn('⚠ Task generation not needed. Active templates above threshold.');
            $this->info('💡 Use --force to generate anyway');
            return Command::SUCCESS;
        }

        if ($this->option('force')) {
            $this->warn('⚡ Forced generation mode enabled');
        }

        $this->newLine();
        $this->info('Starting AI task generation...');
        $this->newLine();

        // Generate all tasks
        $results = $this->taskGenerator->generateAllTasks();

        // Display results
        $this->displayResults($results);

        $duration = round(microtime(true) - $startTime, 2);
        $totalGenerated = $results['surveys'] + $results['videos'] + $results['syncs'] + $results['reviews'];

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ Task Generation Complete!');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("📊 Total Generated: {$totalGenerated} tasks");
        $this->info("⏱  Duration: {$duration}s");

        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error('⚠ Errors occurred:');
            foreach ($results['errors'] as $error) {
                $this->error('  • ' . $error);
            }
        }

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        Log::info('Task generation command completed', [
            'total_generated' => $totalGenerated,
            'duration' => $duration,
            'results' => $results
        ]);

        return Command::SUCCESS;
    }

    /**
     * Display generation results in a nice table
     */
    protected function displayResults($results)
    {
        $this->table(
            ['Task Type', 'Generated', 'Status'],
            [
                ['Surveys', $results['surveys'], $results['surveys'] > 0 ? '✓' : '✗'],
                ['Videos', $results['videos'], $results['videos'] > 0 ? '✓' : '✗'],
                ['App Syncs', $results['syncs'], $results['syncs'] > 0 ? '✓' : '✗'],
                ['Reviews', $results['reviews'], $results['reviews'] > 0 ? '✓' : '✗'],
            ]
        );

        if ($results['surveys'] > 0) {
            $this->line("  ✓ {$results['surveys']} survey tasks created");
        }
        if ($results['videos'] > 0) {
            $this->line("  ✓ {$results['videos']} video tasks created");
        }
        if ($results['syncs'] > 0) {
            $this->line("  ✓ {$results['syncs']} sync tasks created");
        }
        if ($results['reviews'] > 0) {
            $this->line("  ✓ {$results['reviews']} review tasks created");
        }

        $this->newLine();
        $this->warn('⚠ Note: Generated tasks are INACTIVE by default');
        $this->info('💡 Admin must review and activate tasks in dashboard');
    }
}
