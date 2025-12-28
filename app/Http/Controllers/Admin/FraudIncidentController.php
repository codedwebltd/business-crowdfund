<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudIncident;
use App\Models\GlobalSetting;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FraudIncidentController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = FraudIncident::with('user:id,full_name,email,task_ban_until')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('incident_type')) {
            $query->where('incident_type', $request->incident_type);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $incidents = $query->paginate(20)->withQueryString();

        // Calculate stats
        $stats = [
            'total' => FraudIncident::count(),
            'today' => FraudIncident::whereDate('created_at', today())->count(),
            'active_bans' => FraudIncident::whereNotNull('banned_until')
                ->where('banned_until', '>', now())
                ->distinct('user_id')
                ->count(),
            'high_severity' => FraudIncident::where('severity', 'HIGH')
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->count()
        ];

        return Inertia::render('Admin/FraudIncidents/Index', [
            'incidents' => $incidents,
            'stats' => $stats,
            'settings' => GlobalSetting::first()
        ]);
    }

    public function unsuspend($userId)
    {
        $user = \App\Models\User::findOrFail($userId);

        $user->update(['task_ban_until' => null]);

        // Notify user via all available channels
        $this->notificationService->send($user, 'account_unsuspended', [
            'message' => 'Good news! Your task access has been restored. You can now complete tasks and earn rewards again. Please review our Terms of Service to avoid future suspensions.',
            'unsuspended_at' => now()->toDateTimeString(),
            'admin_action' => true
        ]);

        return back()->with('success', 'User unsuspended successfully and notified');
    }

    public function resolve($id)
    {
        $incident = FraudIncident::findOrFail($id);

        // Mark as resolved by updating action_taken
        $incident->update([
            'action_taken' => 'RESOLVED_' . $incident->action_taken
        ]);

        // Notify user if incident was resolved without ban
        if ($incident->user && !$incident->banned_until) {
            $this->notificationService->send($incident->user, 'incident_resolved', [
                'message' => 'A security incident flagged on your account has been reviewed and resolved. Thank you for your cooperation.',
                'incident_type' => $incident->incident_type,
                'resolved_at' => now()->toDateTimeString()
            ]);
        }

        return back()->with('success', 'Incident marked as resolved');
    }
}
