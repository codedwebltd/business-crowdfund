<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\DeviceFingerprint;
use Symfony\Component\HttpFoundation\Response;

class CaptureDeviceFingerprint
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && $request->has('device_fingerprint')) {
            $fingerprintHash = $request->input('device_fingerprint');
            $userId = auth()->id();

            $fingerprint = DeviceFingerprint::where('user_id', $userId)
                ->where('fingerprint_hash', $fingerprintHash)
                ->first();

            if ($fingerprint) {
                // Update existing fingerprint
                $fingerprint->update([
                    'usage_count' => $fingerprint->usage_count + 1,
                    'last_seen_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } else {
                // Create new fingerprint
                DeviceFingerprint::create([
                    'user_id' => $userId,
                    'fingerprint_hash' => $fingerprintHash,
                    'fingerprint_data' => $request->input('fingerprint_details', []),
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                    'usage_count' => 1,
                    'first_seen_at' => now(),
                    'last_seen_at' => now(),
                ]);
            }
        }

        return $next($request);
    }
}
