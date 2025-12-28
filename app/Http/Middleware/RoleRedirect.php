<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirect
{
    /**
     * Redirect users based on role after authentication.
     * Role 1 = Admin → /admin
     * Role 0 = User → /dashboard
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Admin accessing user routes → redirect to admin
            if ($user->isAdmin() && !$request->is('admin*')) {
                return redirect('/admin');
            }

            // User accessing admin routes → redirect to dashboard
            if (!$user->isAdmin() && $request->is('admin*')) {
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
