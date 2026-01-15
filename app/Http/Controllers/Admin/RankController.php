<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RankController extends Controller
{
    public function index()
    {
        $ranks = Rank::withCount('users')
            ->orderBy('order')
            ->get();

        // Calculate stats
        $stats = [
            'total_ranks' => Rank::count(),
            'active_ranks' => Rank::where('is_active', true)->count(),
            'inactive_ranks' => Rank::where('is_active', false)->count(),
            'total_ranked_users' => User::whereNotNull('rank_id')->count(),
            'unranked_users' => User::whereNull('rank_id')->count(),
        ];

        $settings = \App\Models\GlobalSetting::first();

        return Inertia::render('Admin/Ranks/Index', [
            'ranks' => $ranks,
            'stats' => $stats,
            'settings' => $settings,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:ranks,name|max:255',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|unique:ranks,order|min:1',
            'badge_color' => 'required|string|max:7',
            'icon' => 'nullable|string|max:255',

            // Criteria
            'criteria.min_direct_referrals' => 'required|integer|min:0',
            'criteria.min_team_size' => 'required|integer|min:0',
            'criteria.min_monthly_volume' => 'required|integer|min:0',

            // Benefits
            'benefits.withdrawal_min' => 'required|integer|min:0',
            'benefits.withdrawal_max' => 'required|integer|min:0',
            'benefits.withdrawals_per_day' => 'required|integer|min:1',
            'benefits.commission_multiplier' => 'required|numeric|min:1|max:2',
            'benefits.processing_hours' => 'required|string',
        ]);

        Rank::create([
            'name' => strtolower($request->name),
            'display_name' => $request->display_name,
            'description' => $request->description,
            'order' => $request->order,
            'badge_color' => $request->badge_color,
            'icon' => $request->icon,
            'criteria' => $request->criteria,
            'benefits' => $request->benefits,
            'is_active' => true,
        ]);

        return back()->with('success', 'Rank created successfully');
    }

    public function update(Request $request, Rank $rank)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ranks,name,' . $rank->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:1|unique:ranks,order,' . $rank->id,
            'badge_color' => 'required|string|max:7',
            'icon' => 'nullable|string|max:255',

            // Criteria
            'criteria.min_direct_referrals' => 'required|integer|min:0',
            'criteria.min_team_size' => 'required|integer|min:0',
            'criteria.min_monthly_volume' => 'required|integer|min:0',

            // Benefits
            'benefits.withdrawal_min' => 'required|integer|min:0',
            'benefits.withdrawal_max' => 'required|integer|min:0',
            'benefits.withdrawals_per_day' => 'required|integer|min:1',
            'benefits.commission_multiplier' => 'required|numeric|min:1|max:2',
            'benefits.processing_hours' => 'required|string',
        ]);

        $rank->update([
            'name' => strtolower($request->name),
            'display_name' => $request->display_name,
            'description' => $request->description,
            'order' => $request->order,
            'badge_color' => $request->badge_color,
            'icon' => $request->icon,
            'criteria' => $request->criteria,
            'benefits' => $request->benefits,
        ]);

        return back()->with('success', 'Rank updated successfully');
    }

    public function toggleStatus(Rank $rank)
    {
        $rank->update(['is_active' => !$rank->is_active]);

        return back()->with('success', 'Rank status updated successfully');
    }

    public function destroy(Rank $rank)
    {
        // Check if any users have this rank
        if ($rank->users()->count() > 0) {
            return back()->with('error', 'Cannot delete rank with active users. Please reassign users first.');
        }

        $rank->delete();

        return back()->with('success', 'Rank deleted successfully');
    }
}
