<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        // Check for recent fraud incidents (for reCAPTCHA trigger)
        $hasRecentFraud = false;
        if ($user) {
            $recentFraudCount = \App\Models\FraudIncident::where('user_id', $user->id)
                ->where('created_at', '>', now()->subDays(7))
                ->count();
            $hasRecentFraud = $recentFraudCount > 0;
        }

        // Get global settings for reCAPTCHA
        $globalSettings = \App\Models\GlobalSetting::first();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'unreadNotifications' => $user ? $user->unreadNotifications()->count() : 0,
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
            'hasRecentFraud' => $hasRecentFraud,
            'globalSettings' => $globalSettings ? [
                'recaptcha_enabled' => $globalSettings->recaptcha_enabled,
                'recaptcha_site_key' => $globalSettings->recaptcha_site_key,
            ] : null,
        ];
    }
}
