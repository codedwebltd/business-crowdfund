<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendAnnouncementNotificationJob;
use App\Models\Announcement;
use App\Models\GlobalSetting;
use App\Models\User;
use App\Services\GroqService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::query()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $announcements->count(),
            'active' => $announcements->where('is_active', true)->count(),
            'inactive' => $announcements->where('is_active', false)->count(),
            'scheduled' => $announcements->where('start_date', '>', now())->count(),
        ];

        return Inertia::render('Admin/Announcements/Index', [
            'announcements' => $announcements,
            'stats' => $stats,
            'settings' => GlobalSetting::first(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Announcements/Form', [
            'settings' => GlobalSetting::first(),
        ]);
    }

    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'title' => 'required|string|max:255',
        //     'message' => 'required|string',
        //     'type' => 'required|in:info,warning,success,danger',
        //     'priority' => 'required|integer|min:0',
        //     'is_active' => 'boolean',
        //     'is_dismissable' => 'boolean',
        //     'target_audience' => 'required|in:all,active,pending,unverified',
        //     'start_date' => 'nullable|date',
        //     'end_date' => 'nullable|date|after:start_date',
        //     'link_url' => 'nullable|url',
        //     'link_text' => 'nullable|string|max:50',
        //     'send_email' => 'boolean',
        // ]);
        $validated = $request->validate([
    'title' => 'required|string|max:255',
    'message' => 'required|string',
    'type' => 'required|in:info,warning,success,danger',
    'priority' => 'required|integer|min:0',
    'is_active' => 'boolean',
    'is_dismissable' => 'boolean',
    'target_audience' => 'required|in:all,active,pending,unverified',
    'start_date' => 'nullable|date_format:Y-m-d',  // Changed to simple date format
    'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',  // Changed
    'link_url' => 'nullable|url',
    'link_text' => 'nullable|string|max:50',
    'send_email' => 'boolean',
]);

        $sendEmail = $validated['send_email'] ?? false;
        unset($validated['send_email']);

        // Ensure dates are stored as pure dates (no time component)
        if (isset($validated['start_date']) && $validated['start_date']) {
            $validated['start_date'] = \Carbon\Carbon::parse($validated['start_date'])->format('Y-m-d');
        }
        if (isset($validated['end_date']) && $validated['end_date']) {
            $validated['end_date'] = \Carbon\Carbon::parse($validated['end_date'])->format('Y-m-d');
        }

        $announcement = Announcement::create($validated);

        if ($sendEmail) {
            $this->sendNotifications($announcement);
        }

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement created successfully!');
    }

    public function edit(Announcement $announcement)
    {
        return Inertia::render('Admin/Announcements/Form', [
            'announcement' => $announcement,
            'settings' => GlobalSetting::first(),
        ]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        // $validated = $request->validate([
        //     'title' => 'required|string|max:255',
        //     'message' => 'required|string',
        //     'type' => 'required|in:info,warning,success,danger',
        //     'priority' => 'required|integer|min:0',
        //     'is_active' => 'boolean',
        //     'is_dismissable' => 'boolean',
        //     'target_audience' => 'required|in:all,active,pending,unverified',
        //     'start_date' => 'nullable|date',
        //     'end_date' => 'nullable|date|after:start_date',
        //     'link_url' => 'nullable|url',
        //     'link_text' => 'nullable|string|max:50',
        // ]);

        $validated = $request->validate([
    'title' => 'required|string|max:255',
    'message' => 'required|string',
    'type' => 'required|in:info,warning,success,danger',
    'priority' => 'required|integer|min:0',
    'is_active' => 'boolean',
    'is_dismissable' => 'boolean',
    'target_audience' => 'required|in:all,active,pending,unverified',
    'start_date' => 'nullable|date_format:Y-m-d',  // Changed to simple date format
    'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',  // Changed
    'link_url' => 'nullable|url',
    'link_text' => 'nullable|string|max:50',
    'send_email' => 'boolean',
]);

        // Ensure dates are stored as pure dates (no time component)
        if (isset($validated['start_date']) && $validated['start_date']) {
            $validated['start_date'] = \Carbon\Carbon::parse($validated['start_date'])->format('Y-m-d');
        }
        if (isset($validated['end_date']) && $validated['end_date']) {
            $validated['end_date'] = \Carbon\Carbon::parse($validated['end_date'])->format('Y-m-d');
        }

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted successfully!');
    }

    public function sendNotifications(Announcement $announcement)
    {
        $query = User::query();

        if ($announcement->target_audience !== 'all') {
            $query->where('status', strtoupper($announcement->target_audience));
        }

        $userIds = $query->where(function($q) {
            $q->whereJsonContains('notification_preferences->email', true)
              ->orWhereNull('notification_preferences');
        })->pluck('id')->toArray();

        if (empty($userIds)) {
            return back()->with('info', 'No users match the notification criteria.');
        }

        $chunks = array_chunk($userIds, 100);
        $jobs = [];

        foreach ($chunks as $chunk) {
            $jobs[] = new SendAnnouncementNotificationJob($announcement->id, $chunk);
        }

        Bus::batch($jobs)
            ->name('Announcement Notifications - ' . $announcement->title)
            ->onQueue('default')
            ->dispatch();

        return back()->with('success', "Email notifications queued for " . count($userIds) . " users!");
    }

    public function generateWithAI(Request $request, GroqService $groq)
    {
        $validated = $request->validate(['description' => 'required|string|max:500']);

        $systemPrompt = "You are an expert announcement writer. Create professional, engaging announcements for a task-earning platform. Keep it concise, clear, and action-oriented.";

        $userPrompt = "Write an announcement based on this description: {$validated['description']}\n\nFormat:\nTitle: [engaging title]\nMessage: [2-3 sentences, clear call-to-action]";

        $content = $groq->generate($systemPrompt, $userPrompt, 2000, 0.7);

        if (!$content) {
            return response()->json(['error' => 'AI generation failed. Check API key.'], 500);
        }

        preg_match('/Title:\s*(.+?)(?=\n|Message:)/s', $content, $titleMatch);
        preg_match('/Message:\s*(.+)/s', $content, $messageMatch);

        return response()->json([
            'title' => trim($titleMatch[1] ?? ''),
            'message' => trim($messageMatch[1] ?? $content),
        ]);
    }
}
