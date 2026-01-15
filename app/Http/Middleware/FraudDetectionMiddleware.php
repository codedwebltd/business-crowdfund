<?php

namespace App\Http\Middleware;

use App\Services\FraudDetectionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FraudDetectionMiddleware
{
    protected FraudDetectionService $fraudService;

    public function __construct(FraudDetectionService $fraudService)
    {
        $this->fraudService = $fraudService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Skip fraud detection for admins (role_id = 1)
            if ($user->role_id == 1) {
                return $next($request);
            }

            $deviceData = $this->fraudService->extractLocationData();
            $currentIP = $deviceData['ip'] ?? $request->ip();

            Log::info('🔍 Fraud Check on Request', [
                'user_id' => $user->id,
                'route' => $request->path(),
                'ip' => $currentIP,
            ]);

            // Queue async VPN check (no blocking)
            \App\Jobs\CheckVpnFraud::dispatch($user, $currentIP, $request->path());

            // Log IP address (lightweight, no external API)
            $this->fraudService->logIpAddress($user, $currentIP);
        }

        return $next($request);
    }
}
