<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\GlobalSetting;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::with('user')
            ->latest()
            ->get();

        $stats = [
            'pending' => Testimonial::where('status', 'PENDING')->count(),
            'approved' => Testimonial::where('status', 'APPROVED')->count(),
            'rejected' => Testimonial::where('status', 'REJECTED')->count(),
            'auto_approved' => Testimonial::where('auto_approved', true)->count(),
            'trash' => Testimonial::where('trash_testimonial', true)->count(),
            'negative' => Testimonial::where('negative_testimonial', true)->count(),
            'complaint' => Testimonial::where('complaint_testimonial', true)->count(),
        ];

        return Inertia::render('Admin/Testimonials', [
            'testimonials' => $testimonials,
            'stats' => $stats,
            'settings' => GlobalSetting::first(),
        ]);
    }

    public function approve($id, NotificationService $notificationService)
    {
        $testimonial = Testimonial::findOrFail($id);

        $testimonial->update([
            'status' => 'APPROVED',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'admin_notes' => 'Manually approved by admin',
        ]);

        // Send notification to user
        $notificationService->send(
            $testimonial->user,
            'testimonial_approved',
            [
                'testimonial_id' => $testimonial->id,
                'auto_approved' => false,
            ]
        );

        return back()->with('success', 'Testimonial approved successfully!');
    }

    public function reject(Request $request, $id, NotificationService $notificationService)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $testimonial = Testimonial::findOrFail($id);

        $testimonial->update([
            'status' => 'REJECTED',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'admin_notes' => $request->reason,
        ]);

        // Send notification to user
        $notificationService->send(
            $testimonial->user,
            'testimonial_rejected',
            [
                'testimonial_id' => $testimonial->id,
                'reason' => $request->reason,
            ]
        );

        return back()->with('success', 'Testimonial rejected successfully!');
    }
}
