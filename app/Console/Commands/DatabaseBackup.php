<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--filename=backup.sql}';
    protected $description = 'Backup the database';

    public function handle()
    {
        $filename = $this->option('filename');
        $backupPath = storage_path('backups/' . $filename);

        // Ensure backup directory exists
        if (!is_dir(storage_path('backups'))) {
            mkdir(storage_path('backups'), 0755, true);
        }

        // Get database credentials
        $dbHost = config('database.connections.mysql.host');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        // Build mysqldump command using Process
        $process = Process::fromShellCommandline(sprintf(
            'mysqldump -h %s -u %s -p%s %s > %s 2>&1',
            escapeshellarg($dbHost),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbName),
            escapeshellarg($backupPath)
        ));

        $process->setTimeout(300); // 5 minutes timeout
        $process->run();

        if ($process->isSuccessful() && file_exists($backupPath)) {
            $this->info("✅ Database backup created: {$backupPath}");
            $size = round(filesize($backupPath) / 1024 / 1024, 2);
            $this->info("📦 Size: {$size} MB");
            return 0;
        } else {
            $this->error("❌ Database backup failed!");
            $this->error($process->getOutput());
            $this->error($process->getErrorOutput());
            return 1;
        }
    }
}
