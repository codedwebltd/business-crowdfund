<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlanSelected
{
    /**
     * Handle an incoming request.
     * Redirect to plan selection if user has no active subscription
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Skip if no user (shouldn't happen with auth middleware)
        if (!$user) {
            return $next($request);
        }

        // Skip if already on plan selection page
        if ($request->routeIs('plan.selection') || $request->routeIs('plan.select')) {
            return $next($request);
        }

        // Check if user has an active subscription
        $hasActiveSubscription = $user->activeSubscription()->exists();

        if (!$hasActiveSubscription) {
            return redirect()->route('plan.selection');
        }

        return $next($request);
    }
}
