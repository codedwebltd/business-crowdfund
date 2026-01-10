<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Transform notifications for frontend
        $transformed = $notifications->getCollection()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->data['title'] ?? 'Notification',
                'message' => $notification->data['message'] ?? '',
                'action_url' => $notification->data['action_url'] ?? null,
                'action_label' => $notification->data['action_label'] ?? null,
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
            ];
        });

        return Inertia::render('User/Notifications', [
            'notifications' => $transformed->values()->all(),
            'hasMore' => $notifications->hasMorePages(),
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return back();
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return back();
    }

    public function destroy(Request $request, $id)
    {
        $request->user()
            ->notifications()
            ->where('id', $id)
            ->delete();

        return back();
    }

    public function deleteAll(Request $request)
    {
        $request->user()->notifications()->delete();
        return back();
    }

    public function check(Request $request)
    {
        $user = $request->user();
        $unreadCount = $user->unreadNotifications()->count();
        $latestNotification = $user->unreadNotifications()->latest()->first();

        $latestData = null;
        if ($latestNotification) {
            $latestData = [
                'id' => $latestNotification->id,
                'title' => $latestNotification->data['title'] ?? 'Notification',
                'message' => $latestNotification->data['message'] ?? '',
                'action_url' => $latestNotification->data['action_url'] ?? null,
                'action_label' => $latestNotification->data['action_label'] ?? null,
            ];
        }

        return response()->json([
            'unread_count' => $unreadCount,
            'latest_notification' => $latestData,
        ]);
    }
}
