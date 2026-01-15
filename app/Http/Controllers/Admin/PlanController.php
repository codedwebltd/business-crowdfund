<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::with('rank')
            ->withCount('users')
            ->orderBy('order')
            ->get();

        // Calculate stats
        $stats = [
            'total_plans' => Plan::count(),
            'active_plans' => Plan::where('is_active', true)->count(),
            'inactive_plans' => Plan::where('is_active', false)->count(),
            'total_subscribed_users' => User::whereNotNull('plan_id')->count(),
            'total_revenue' => \App\Models\Transaction::whereIn('transaction_type', ['ACTIVATION_PAYMENT', 'PLAN_UPGRADE'])
                ->where('status', 'COMPLETED')
                ->sum('amount'),
            'popular_plan' => Plan::where('is_popular', true)->first()?->display_name ?? 'None',
        ];

        $ranks = Rank::where('is_active', true)->orderBy('order')->get();
        $settings = \App\Models\GlobalSetting::first();

        return Inertia::render('Admin/Plans/Index', [
            'plans' => $plans,
            'stats' => $stats,
            'ranks' => $ranks,
            'settings' => $settings,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:plans,name|max:255',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|unique:plans,order|min:1',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'badge_color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
            'is_popular' => 'boolean',
            'rank_id' => 'nullable|exists:ranks,id',

            // Features
            'features.max_daily_tasks' => 'required|integer|min:1',
            'features.daily_earning_potential' => 'required|integer|min:0',
            'features.referral_bonus_percentage' => 'required|integer|min:0|max:100',
            'features.task_reward_multiplier' => 'required|numeric|min:1|max:3',
            'features.priority_support' => 'boolean',
            'features.feature_list' => 'required|array|min:1',
            'features.feature_list.*' => 'required|string',
        ]);

        Plan::create([
            'name' => strtolower($request->name),
            'display_name' => $request->display_name,
            'description' => $request->description,
            'order' => $request->order,
            'price' => $request->price,
            'currency' => $request->currency,
            'badge_color' => $request->badge_color,
            'icon' => $request->icon,
            'is_popular' => $request->is_popular ?? false,
            'is_active' => true,
            'rank_id' => $request->rank_id,
            'features' => $request->features,
        ]);

        return back()->with('success', 'Plan created successfully');
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $plan->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:1|unique:plans,order,' . $plan->id,
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'badge_color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
            'is_popular' => 'boolean',
            'rank_id' => 'nullable|exists:ranks,id',

            // Features
            'features.max_daily_tasks' => 'required|integer|min:1',
            'features.daily_earning_potential' => 'required|integer|min:0',
            'features.referral_bonus_percentage' => 'required|integer|min:0|max:100',
            'features.task_reward_multiplier' => 'required|numeric|min:1|max:3',
            'features.priority_support' => 'boolean',
            'features.feature_list' => 'required|array|min:1',
            'features.feature_list.*' => 'required|string',
        ]);

        $plan->update([
            'name' => strtolower($request->name),
            'display_name' => $request->display_name,
            'description' => $request->description,
            'order' => $request->order,
            'price' => $request->price,
            'currency' => $request->currency,
            'badge_color' => $request->badge_color,
            'icon' => $request->icon,
            'is_popular' => $request->is_popular ?? false,
            'rank_id' => $request->rank_id,
            'features' => $request->features,
        ]);

        return back()->with('success', 'Plan updated successfully');
    }

    public function toggleStatus(Plan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        return back()->with('success', 'Plan status updated successfully');
    }

    public function togglePopular(Plan $plan)
    {
        // Remove popular status from all other plans
        Plan::where('id', '!=', $plan->id)->update(['is_popular' => false]);

        // Toggle this plan
        $plan->update(['is_popular' => !$plan->is_popular]);

        return back()->with('success', 'Popular plan updated successfully');
    }

    public function destroy(Plan $plan)
    {
        // Check if any users have this plan
        if ($plan->users()->count() > 0) {
            return back()->with('error', 'Cannot delete plan with active users. Please reassign users first.');
        }

        $plan->delete();

        return back()->with('success', 'Plan deleted successfully');
    }
}
