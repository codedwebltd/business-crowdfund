<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskContentPool;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContentPoolController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskContentPool::query();

        // Filters
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->source) {
            $query->where('source', $request->source);
        }
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Stats
        $stats = [
            'total' => TaskContentPool::count(),
            'active' => TaskContentPool::where('is_active', true)->count(),
            'unused' => TaskContentPool::whereNull('last_used_at')->count(),
            'videos' => TaskContentPool::where('type', 'VIDEO')->count(),
            'surveys' => TaskContentPool::where('type', 'SURVEY')->count(),
            'reviews' => TaskContentPool::where('type', 'REVIEW')->count(),
        ];

        $pools = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/ContentPool/Index', [
            'pools' => $pools,
            'stats' => $stats,
            'filters' => $request->only(['type', 'source', 'status', 'search']),
            'settings' => GlobalSetting::first(),
        ]);
    }

    public function toggleActive(Request $request, $id)
    {
        $pool = TaskContentPool::findOrFail($id);
        $pool->is_active = !$pool->is_active;
        $pool->save();

        return back()->with('success', 'Pool item ' . ($pool->is_active ? 'activated' : 'deactivated'));
    }

    public function destroy($id)
    {
        $pool = TaskContentPool::findOrFail($id);
        $pool->delete();

        return back()->with('success', 'Pool item deleted');
    }

    public function resetUsage($id)
    {
        $pool = TaskContentPool::findOrFail($id);
        $pool->last_used_at = null;
        $pool->times_used = 0;
        $pool->save();

        return back()->with('success', 'Pool item usage reset');
    }
}
