<?php

namespace App\Jobs;

use App\Models\TaskTemplate;
use App\Models\User;
use App\Models\UserTask;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AssignTaskBatch implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $userId,
        public array $taskTemplateIds,
        public int $expirationHours = 24
    ) {}

    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $user = User::find($this->userId);
        if (!$user) return;

        DB::beginTransaction();
        try {
            foreach ($this->taskTemplateIds as $templateId) {
                $template = TaskTemplate::find($templateId);
                if (!$template || !$template->is_active) continue;

                // Check if already assigned today
                $exists = UserTask::where('user_id', $user->id)
                    ->where('task_template_id', $template->id)
                    ->whereDate('assigned_at', today())
                    ->exists();

                if ($exists) continue;

                UserTask::create([
                    'user_id' => $user->id,
                    'task_template_id' => $template->id,
                    'status' => 'PENDING',
                    'assigned_at' => now(),
                    'expires_at' => now()->addHours($this->expirationHours),
                    'reward_amount' => $template->reward_amount,
                    'credited' => false,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error("Task batch assignment failed for user {$user->id}: " . $e->getMessage());
        }
    }
}
