<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskTemplate;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskTemplate::query();

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->latest()->paginate(15);

        $stats = [
            'total' => TaskTemplate::count(),
            'active' => TaskTemplate::where('is_active', true)->count(),
            'pending' => TaskTemplate::where('is_active', false)->count(),
            'ai_generated' => TaskTemplate::whereNotNull('created_at')->whereDate('created_at', '>=', now()->subDays(7))->count(),
        ];

        return Inertia::render('Admin/Tasks/Index', [
            'tasks' => $tasks,
            'stats' => $stats,
            'settings' => GlobalSetting::first(),
        ]);
    }

    public function approve($id)
    {
        TaskTemplate::findOrFail($id)->update(['is_active' => true]);
        return back()->with('success', 'Task approved and activated');
    }

    public function deactivate($id)
    {
        TaskTemplate::findOrFail($id)->update(['is_active' => false]);
        return back()->with('success', 'Task deactivated');
    }

    public function destroy($id)
    {
        TaskTemplate::findOrFail($id)->delete();
        return back()->with('success', 'Task deleted');
    }
}
