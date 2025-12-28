<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPlan
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Allow admins (role 1) to bypass plan requirement
        if ($user->role === 1) {
            return $next($request);
        }

        // Allow access to dashboard (handles plan/pending logic internally)
        if ($request->is('dashboard')) {
            return $next($request);
        }

        // Allow access to payment routes
        if ($request->is('payment/*') || $request->is('qr-code')) {
            return $next($request);
        }

        // Allow logout
        if ($request->is('logout')) {
            return $next($request);
        }

        // If user doesn't have a plan, redirect to dashboard
        if (!$user->plan_id) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
