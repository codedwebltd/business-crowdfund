<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GitController extends Controller
{
    /**
     * Execute git push with database backup
     */
    public function push(Request $request)
    {
        $request->validate([
            'commit_message' => 'required|string|min:10|max:500',
            'backup_database' => 'boolean',
        ]);

        // Sanitize and format commit message
        $commitMessage = $this->sanitizeCommitMessage($request->commit_message);
        $backupDatabase = $request->backup_database ?? true;
        $output = '';
        $backupFile = null;

        try {
            // Change to project directory
            $projectPath = base_path();

            // Backup database if requested
            if ($backupDatabase) {
                $output .= "📦 Creating database backup...\n";

                try {
                    $timestamp = date('Ymd_His');
                    $backupFilename = "backup_{$timestamp}.sql";

                    // Run Laravel backup command
                    Artisan::call('db:backup', ['--filename' => $backupFilename]);

                    $backupFile = "storage/backups/{$backupFilename}";
                    $output .= "✅ Database backup created: {$backupFilename}\n\n";
                } catch (\Exception $e) {
                    $output .= "⚠️ Database backup failed: {$e->getMessage()}\n";
                    $output .= "Continuing with git push...\n\n";
                }
            }

            // Git operations
            $output .= "📤 Executing git operations...\n\n";

            // Git add all changes
            $output .= "$ git add .\n";
            $process = new Process(['git', 'add', '.'], $projectPath);
            $process->run();
            $output .= $process->getOutput() . $process->getErrorOutput() . "\n";

            // Git status
            $output .= "$ git status\n";
            $process = new Process(['git', 'status'], $projectPath);
            $process->run();
            $output .= $process->getOutput() . "\n";

            // Git commit
            $output .= "$ git commit -m \"{$commitMessage}\"\n";
            $process = new Process(['git', 'commit', '-m', $commitMessage], $projectPath);
            $process->run();
            $commitOutput = $process->getOutput();
            $output .= $commitOutput . "\n";

            // Extract files changed info
            $filesChanged = 'N/A';
            if (preg_match('/(\d+) file[s]? changed/', $commitOutput, $matches)) {
                $filesChanged = $matches[1];
            }

            // Git push
            $output .= "$ git push origin main\n";
            $process = new Process(['git', 'push', 'origin', 'main'], $projectPath);
            $process->setTimeout(120); // 2 minutes timeout
            $process->run();

            $pushOutput = $process->getOutput() . $process->getErrorOutput();
            $output .= $pushOutput . "\n";

            // Check if push was successful
            if ($process->isSuccessful()) {
                $output .= "✅ Git push completed successfully!\n";

                // Log the git push
                Log::info('Admin Git Push', [
                    'user' => auth()->user()->email,
                    'commit_message' => $commitMessage,
                    'files_changed' => $filesChanged,
                    'backup_file' => $backupFile,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Git push successful',
                    'output' => $output,
                    'commit_message' => $commitMessage,
                    'files_changed' => $filesChanged,
                    'backup_file' => $backupFile,
                ]);
            } else {
                $output .= "❌ Git push failed!\n";
                $output .= "Exit code: " . $process->getExitCode() . "\n";

                return response()->json([
                    'success' => false,
                    'message' => 'Git push failed. Check output for details.',
                    'output' => $output,
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Git Push Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'output' => $output . "\n\n❌ Exception: " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get git status
     */
    public function status()
    {
        $projectPath = base_path();

        try {
            $process = new Process(['git', 'status'], $projectPath);
            $process->run();

            return response()->json([
                'success' => true,
                'output' => $process->getOutput(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recent commits
     */
    public function log()
    {
        $projectPath = base_path();

        try {
            $process = new Process(['git', 'log', '--oneline', '-10'], $projectPath);
            $process->run();

            return response()->json([
                'success' => true,
                'output' => $process->getOutput(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sanitize commit message for safe shell execution
     */
    private function sanitizeCommitMessage(string $message): string
    {
        // Trim whitespace
        $message = trim($message);

        // Remove or replace problematic characters
        // Replace newlines with spaces
        $message = preg_replace('/\r\n|\r|\n/', ' ', $message);

        // Remove multiple spaces
        $message = preg_replace('/\s+/', ' ', $message);

        // Remove quotes that could break the shell command (we'll add them back safely)
        $message = str_replace(['"', "'", '`'], '', $message);

        // Remove any null bytes
        $message = str_replace("\0", '', $message);

        // Trim again after sanitization
        $message = trim($message);

        // Ensure message is not empty after sanitization
        if (empty($message)) {
            $message = 'Update files';
        }

        return $message;
    }

    /**
     * Get uncommitted files list
     */
    public function uncommitted()
    {
        $projectPath = base_path();

        try {
            $process = new Process(['git', 'status', '--porcelain'], $projectPath);
            $process->run();

            $output = trim($process->getOutput());
            $files = [];

            if (!empty($output)) {
                $lines = explode("\n", $output);
                foreach ($lines as $line) {
                    if (empty($line)) continue;

                    // Parse git status --porcelain format
                    // Format: XY PATH or XY PATH -> NEW_PATH
                    $status = trim(substr($line, 0, 2));
                    $path = trim(substr($line, 3));

                    // Map status codes to readable status
                    $statusMap = [
                        'M' => 'M',   // Modified
                        'A' => 'A',   // Added
                        'D' => 'D',   // Deleted
                        'R' => 'R',   // Renamed
                        'C' => 'C',   // Copied
                        'U' => 'U',   // Updated
                        '??' => '??', // Untracked
                        'MM' => 'M',  // Modified (staged and unstaged)
                        ' M' => 'M',  // Modified (unstaged)
                        'M ' => 'M',  // Modified (staged)
                        'AM' => 'A',  // Added (modified)
                        'A ' => 'A',  // Added (staged)
                    ];

                    $displayStatus = $statusMap[$status] ?? $status;

                    $files[] = [
                        'status' => $displayStatus,
                        'path' => $path,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'files' => $files,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get git repository info
     */
    public function info()
    {
        $projectPath = base_path();

        try {
            // Get current branch
            $process = new Process(['git', 'rev-parse', '--abbrev-ref', 'HEAD'], $projectPath);
            $process->run();
            $branch = trim($process->getOutput()) ?: 'main';

            // Get remote URL
            $process = new Process(['git', 'config', '--get', 'remote.origin.url'], $projectPath);
            $process->run();
            $url = trim($process->getOutput()) ?: 'Not configured';

            // Get last commit
            $process = new Process(['git', 'log', '-1', '--oneline'], $projectPath);
            $process->run();
            $lastCommit = trim($process->getOutput()) ?: null;

            // Get uncommitted changes count
            $process = new Process(['git', 'status', '--porcelain'], $projectPath);
            $process->run();
            $statusOutput = array_filter(explode("\n", trim($process->getOutput())));
            $uncommittedChanges = count($statusOutput);

            return response()->json([
                'success' => true,
                'info' => [
                    'branch' => $branch,
                    'url' => $url,
                    'lastCommit' => $lastCommit,
                    'uncommittedChanges' => $uncommittedChanges,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate commit message using Groq AI
     */
    public function generateCommitMessage()
    {
        $projectPath = base_path();

        try {
            // Get git diff
            $process = new Process(['git', 'diff', '--cached'], $projectPath);
            $process->run();
            $stagedDiff = $process->getOutput();

            // If no staged changes, get unstaged diff
            if (empty($stagedDiff)) {
                $process = new Process(['git', 'diff'], $projectPath);
                $process->run();
                $stagedDiff = $process->getOutput();
            }

            // Get file status summary
            $process = new Process(['git', 'status', '--short'], $projectPath);
            $process->run();
            $statusSummary = $process->getOutput();

            if (empty($stagedDiff) && empty($statusSummary)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No changes detected to generate commit message.',
                ], 400);
            }

            // Get Groq API configuration
            $settings = GlobalSetting::first();
            $aiConfig = $settings->ai_configuration ?? [];

            if (empty($aiConfig['groq_api_key'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Groq API key not configured in settings.',
                ], 500);
            }

            // Parse status to get changed files with context
            $changedFiles = [];
            $statusLines = array_filter(explode("\n", trim($statusSummary)));
            foreach ($statusLines as $line) {
                if (preg_match('/^(M|A|D|R|C|\?\?)\s+(.+)$/', trim($line), $matches)) {
                    $status = $matches[1];
                    $file = $matches[2];

                    $statusLabel = match($status) {
                        'M' => 'Modified',
                        'A' => 'Added',
                        'D' => 'Deleted',
                        'R' => 'Renamed',
                        'C' => 'Copied',
                        '??' => 'Untracked',
                        default => 'Changed'
                    };

                    $changedFiles[] = "{$statusLabel}: {$file}";
                }
            }

            // Prepare enhanced prompt for Groq with better context
            $prompt = "You are an expert Git commit message generator. Your task is to analyze code changes and generate a highly descriptive, meaningful commit message.\n\n";

            $prompt .= "=== CHANGED FILES ===\n";
            $prompt .= implode("\n", $changedFiles) . "\n\n";

            if (!empty($stagedDiff)) {
                // Increased limit to 8000 chars for better context
                $diffPreview = strlen($stagedDiff) > 8000 ? substr($stagedDiff, 0, 8000) . "\n...(diff truncated for brevity)" : $stagedDiff;
                $prompt .= "=== CODE CHANGES (GIT DIFF) ===\n" . $diffPreview . "\n\n";
            }

            $prompt .= "=== INSTRUCTIONS ===\n";
            $prompt .= "Analyze the above changes carefully and generate a commit message that:\n\n";
            $prompt .= "1. STARTS with a conventional commit type prefix:\n";
            $prompt .= "   - feat: New feature or significant enhancement\n";
            $prompt .= "   - fix: Bug fix or error correction\n";
            $prompt .= "   - update: Updates to existing features (UI improvements, minor changes)\n";
            $prompt .= "   - refactor: Code restructuring without behavior change\n";
            $prompt .= "   - docs: Documentation changes\n";
            $prompt .= "   - style: Formatting, styling (CSS, UI)\n";
            $prompt .= "   - perf: Performance improvements\n";
            $prompt .= "   - test: Adding or updating tests\n";
            $prompt .= "   - chore: Maintenance tasks, dependency updates\n\n";
            $prompt .= "2. DESCRIBES the actual business/functional change, not technical implementation\n";
            $prompt .= "   - Good: 'Add crypto withdrawal tracking feature'\n";
            $prompt .= "   - Bad: 'Update files' or 'Change code'\n\n";
            $prompt .= "3. USES imperative mood (Add/Fix/Update, not Added/Fixed/Updated)\n\n";
            $prompt .= "4. IS SPECIFIC about what changed:\n";
            $prompt .= "   - Mention the feature/component affected\n";
            $prompt .= "   - Include key functionality added or fixed\n";
            $prompt .= "   - Be clear and descriptive\n\n";
            $prompt .= "5. KEEPS length between 50-100 characters\n\n";
            $prompt .= "IMPORTANT: Return ONLY the commit message, no quotes, no explanations, no extra text.";

            // Call Groq API
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $aiConfig['groq_api_key'],
                    'Content-Type' => 'application/json',
                ])
                ->post($aiConfig['groq_endpoint'], [
                    'model' => $aiConfig['groq_model'] ?? 'llama-3.1-8b-instant',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert Git commit message generator. You analyze code changes deeply and create highly descriptive, meaningful commit messages that clearly communicate what changed and why. You follow conventional commit format and best practices.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => $aiConfig['temperature'] ?? 0.5,
                    'max_tokens' => 3000,
                ]);

            if (!$response->successful()) {
                Log::error('Groq API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate commit message: ' . $response->body(),
                ], 500);
            }

            $result = $response->json();
            $commitMessage = $result['choices'][0]['message']['content'] ?? null;

            if (empty($commitMessage)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No commit message generated.',
                ], 500);
            }

            // Clean up the message (remove quotes, extra whitespace)
            $commitMessage = trim($commitMessage, " \n\r\t\v\0\"'");
            $commitMessage = preg_replace('/\s+/', ' ', $commitMessage);

            return response()->json([
                'success' => true,
                'message' => $commitMessage,
            ]);

        } catch (\Exception $e) {
            Log::error('Generate Commit Message Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
