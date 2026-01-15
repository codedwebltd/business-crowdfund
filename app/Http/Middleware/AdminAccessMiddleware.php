<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\GlobalSetting;

class AdminAccessMiddleware
{
    /**
     * Recovery token for admin access when IP is blocked
     * MD5 hash of "Galaxys24ultras."
     */
    const RECOVERY_TOKEN = '8c7dd922ad47494fc02c388e12c00eac';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get user's IP address
        $userIp = $request->ip();

        // Check for recovery token in URL (?admin_token=Galaxys24ultras.)
        $providedToken = $request->query('admin_token');
        if ($providedToken && md5($providedToken) === self::RECOVERY_TOKEN) {
            // Valid recovery token - grant access
            logger()->info("Admin access granted via recovery token from IP: {$userIp}");
            return $next($request);
        }

        // Get global settings for IP whitelist
        $settings = GlobalSetting::first();

        // If no whitelist configured, allow access (backward compatibility)
        if (!$settings || !$settings->admin_ip_whitelist || empty($settings->admin_ip_whitelist)) {
            return $next($request);
        }

        $whitelist = $settings->admin_ip_whitelist;

        // Check if user IP is in whitelist
        if ($this->isIpWhitelisted($userIp, $whitelist)) {
            return $next($request);
        }

        // Access denied
        logger()->warning("Admin access denied for IP: {$userIp}. Use ?admin_token=PASSWORD for recovery.");

        // Return custom 403 error page
        abort(403, 'Access Denied - Your IP address is not authorized.');
    }

    /**
     * Check if an IP address is in the whitelist
     * Supports individual IPs and CIDR ranges
     */
    private function isIpWhitelisted(string $ip, array $whitelist): bool
    {
        foreach ($whitelist as $allowedIp) {
            // Check for CIDR range (e.g., "192.168.1.0/24")
            if (strpos($allowedIp, '/') !== false) {
                if ($this->ipInRange($ip, $allowedIp)) {
                    return true;
                }
            }
            // Check for exact IP match
            elseif ($ip === $allowedIp) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an IP address is within a CIDR range
     */
    private function ipInRange(string $ip, string $range): bool
    {
        list($subnet, $mask) = explode('/', $range);

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int)$mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}
