<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Models\User;
use App\Services\EmailNotificationService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAnnouncementNotificationJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;

    protected $announcementId;
    protected $userIds;

    public function __construct($announcementId, array $userIds)
    {
        $this->announcementId = $announcementId;
        $this->userIds = $userIds;
        $this->onQueue('default');
    }

    public function handle(EmailNotificationService $emailService)
    {
        $announcement = Announcement::find($this->announcementId);

        if (!$announcement) {
            Log::warning("Announcement {$this->announcementId} not found");
            return;
        }

        $users = User::whereIn('id', $this->userIds)->get();

        foreach ($users as $user) {
            try {
                $emailService->sendAnnouncementEmail($user, $announcement);
            } catch (\Exception $e) {
                Log::error("Failed to send announcement to user {$user->id}: {$e->getMessage()}");
            }
        }
    }
}
