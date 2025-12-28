<?php

namespace App\Services;

use App\Models\FraudIncident;
use App\Models\ReferralTree;
use App\Models\User;
use App\Models\UserIpAddress;
use App\Traits\LocationTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FraudDetectionService
{
    use LocationTrait;

    /**
     * Check for fraud patterns during registration
     */
    public function checkRegistrationFraud(User $newUser, ?User $referrer): ?array
    {
        $deviceData = $this->extractLocationData();
        $newUserIP = $deviceData['ip'] ?? request()->ip();
        $newUserFingerprint = $this->generateDeviceFingerprint($deviceData);

        Log::info('🔍 Fraud Detection Started', [
            'user_id' => $newUser->id,
            'user_name' => $newUser->full_name,
            'ip' => $newUserIP,
            'fingerprint' => $newUserFingerprint,
            'referrer_id' => $referrer?->id,
        ]);

        // 1. Check for VPN/Proxy/Tor (CRITICAL - Block immediately)
        $vpnCheck = $this->detectVpnProxy($newUserIP);

        Log::info('🛡️ VPN/Proxy Check', [
            'ip' => $newUserIP,
            'is_vpn' => $vpnCheck['is_vpn'],
            'is_proxy' => $vpnCheck['is_proxy'],
            'is_tor' => $vpnCheck['is_tor'],
            'vpn_data' => $vpnCheck,
        ]);

        if ($vpnCheck['is_vpn'] || $vpnCheck['is_proxy'] || $vpnCheck['is_tor']) {
            Log::warning('🚫 VPN/PROXY BLOCKED', [
                'user_id' => $newUser->id,
                'user_name' => $newUser->full_name,
                'ip' => $newUserIP,
                'vpn_data' => $vpnCheck,
            ]);

            return [
                'fraud_detected' => true,
                'action' => 'BLOCKED_VPN_PROXY',
                'fraud_type' => 'VPN_PROXY_DETECTED',
                'matched_user' => $newUser->full_name,
                'affected_count' => 1,
                'banned_until' => null,
                'details' => 'Registration blocked: VPN, Proxy, or Tor detected. Please use your real IP address.',
                'vpn_data' => $vpnCheck,
            ];
        }

        // 2. Check IP pool for duplicate IP usage (even without referral)
        $ipPoolFraud = $this->checkIpPool($newUserIP, $newUser);
        if ($ipPoolFraud) {
            return $ipPoolFraud;
        }

        // 3. Check referral chain patterns (if has referrer)
        if ($referrer) {
            $referralChain = $this->getReferralChain($referrer);
            $fraudDetected = $this->detectPatterns($newUserIP, $newUserFingerprint, $referralChain);

            if ($fraudDetected) {
                return $this->handleFraudIncident($newUser, $referrer, $fraudDetected, $referralChain);
            }
        }

        // 4. Save IP to pool (clean registration)
        $this->saveIpToPool($newUser, $newUserIP, $deviceData, $vpnCheck);

        return null;
    }

    /**
     * Log IP address for user (used during login and browsing)
     */
    public function logIpAddress(User $user, string $ip): void
    {
        $deviceData = $this->extractLocationData();
        $vpnCheck = $this->detectVpnProxy($ip);

        $ipRecord = UserIpAddress::where('user_id', $user->id)
            ->where('ip_address', $ip)
            ->where('usage_type', 'LOGIN')
            ->first();

        if ($ipRecord) {
            // Update existing record
            $ipRecord->update([
                'ip_type' => $this->getIpType($ip),
                'is_vpn' => $vpnCheck['is_vpn'] ?? false,
                'is_proxy' => $vpnCheck['is_proxy'] ?? false,
                'is_tor' => $vpnCheck['is_tor'] ?? false,
                'is_datacenter' => $vpnCheck['is_datacenter'] ?? false,
                'threat_level' => $vpnCheck['threat_level'] ?? 'LOW',
                'country_code' => $deviceData['country']['code'] ?? null,
                'country' => $deviceData['country']['name'] ?? null,
                'city' => $deviceData['city']['name'] ?? null,
                'isp' => $vpnCheck['isp'] ?? null,
                'organization' => $vpnCheck['organization'] ?? null,
                'detection_data' => $vpnCheck,
                'last_seen_at' => now(),
            ]);
            $ipRecord->increment('usage_count');
        } else {
            // Create new record
            UserIpAddress::create([
                'user_id' => $user->id,
                'ip_address' => $ip,
                'ip_type' => $this->getIpType($ip),
                'is_vpn' => $vpnCheck['is_vpn'] ?? false,
                'is_proxy' => $vpnCheck['is_proxy'] ?? false,
                'is_tor' => $vpnCheck['is_tor'] ?? false,
                'is_datacenter' => $vpnCheck['is_datacenter'] ?? false,
                'threat_level' => $vpnCheck['threat_level'] ?? 'LOW',
                'country_code' => $deviceData['country']['code'] ?? null,
                'country' => $deviceData['country']['name'] ?? null,
                'city' => $deviceData['city']['name'] ?? null,
                'isp' => $vpnCheck['isp'] ?? null,
                'organization' => $vpnCheck['organization'] ?? null,
                'detection_data' => $vpnCheck,
                'usage_type' => 'LOGIN',
            ]);
        }
    }

    /**
     * Detect VPN/Proxy/Tor using multiple detection methods
     */
    public function detectVpnProxy(string $ip): array
    {
        // Skip localhost/private IPs
        if ($this->isPrivateIp($ip)) {
            return [
                'is_vpn' => false,
                'is_proxy' => false,
                'is_tor' => false,
                'is_datacenter' => false,
                'threat_level' => 'LOW',
                'detection_method' => 'private_ip_skipped',
            ];
        }

        // Try multiple detection services (free tier)
        $result = $this->checkProxyCheckIo($ip);
        if (!$result) {
            $result = $this->checkIpQualityScore($ip);
        }
        if (!$result) {
            $result = $this->checkIpApiCom($ip);
        }

        return $result ?? [
            'is_vpn' => false,
            'is_proxy' => false,
            'is_tor' => false,
            'is_datacenter' => false,
            'threat_level' => 'UNKNOWN',
            'detection_method' => 'all_services_failed',
        ];
    }

    /**
     * Check IP pool for duplicate registrations from same IP
     */
    private function checkIpPool(string $ip, User $newUser): ?array
    {
        // Find all previous registrations from this IP
        $previousUsers = UserIpAddress::where('ip_address', $ip)
            ->where('usage_type', 'REGISTRATION')
            ->where('user_id', '!=', $newUser->id)
            ->with('user')
            ->get();

        if ($previousUsers->isEmpty()) {
            return null; // Clean IP
        }

        // Multiple users from same IP = fraud attempt
        $affectedUserIds = $previousUsers->pluck('user_id')->push($newUser->id)->unique()->toArray();
        $matchedUser = $previousUsers->first()->user;

        // Progressive penalty based on IP reuse count
        if ($previousUsers->count() === 1) {
            // First IP reuse: 2 days task ban
            $action = 'TASK_BANNED_2DAYS';
            $bannedUntil = now()->addDays(2);
            $severity = 'MEDIUM';
        } else {
            // Multiple IP reuse: Freeze all accounts
            $action = 'TREE_FROZEN';
            $bannedUntil = null;
            $severity = 'HIGH';
        }

        // Create fraud incident
        FraudIncident::create([
            'user_id' => $newUser->id,
            'incident_type' => 'IP_REUSE',
            'severity' => $severity,
            'incident_data' => [
                'matched_user_id' => $matchedUser->id,
                'matched_user_name' => $matchedUser->full_name,
                'details' => "IP address {$ip} already used by {$previousUsers->count()} other user(s)",
                'ip_address' => $ip,
                'previous_users_count' => $previousUsers->count(),
            ],
            'affected_users' => $affectedUserIds,
            'action_taken' => $action,
            'banned_until' => $bannedUntil,
        ]);

        // Apply penalty
        if ($action === 'TASK_BANNED_2DAYS') {
            User::whereIn('id', $affectedUserIds)->update(['task_ban_until' => $bannedUntil]);
        } else {
            User::whereIn('id', $affectedUserIds)->update(['status' => 'SUSPENDED']);
        }

        return [
            'fraud_detected' => true,
            'action' => $action,
            'fraud_type' => 'IP_REUSE',
            'matched_user' => $matchedUser->full_name,
            'affected_count' => count($affectedUserIds),
            'banned_until' => $bannedUntil,
            'details' => "This IP address has been used by {$previousUsers->count()} other account(s). Suspicious activity detected.",
        ];
    }

    /**
     * Save IP to pool after clean registration
     */
    private function saveIpToPool(User $user, string $ip, array $deviceData, array $vpnCheck): void
    {
        UserIpAddress::create([
            'user_id' => $user->id,
            'ip_address' => $ip,
            'ip_type' => $this->getIpType($ip),
            'is_vpn' => $vpnCheck['is_vpn'] ?? false,
            'is_proxy' => $vpnCheck['is_proxy'] ?? false,
            'is_tor' => $vpnCheck['is_tor'] ?? false,
            'is_datacenter' => $vpnCheck['is_datacenter'] ?? false,
            'threat_level' => $vpnCheck['threat_level'] ?? 'LOW',
            'country_code' => $deviceData['country']['code'] ?? null,
            'country' => $deviceData['country']['name'] ?? null,
            'city' => $deviceData['city']['name'] ?? null,
            'isp' => $vpnCheck['isp'] ?? null,
            'organization' => $vpnCheck['organization'] ?? null,
            'detection_data' => $vpnCheck,
            'usage_type' => 'REGISTRATION',
        ]);
    }

    /**
     * Get referral chain (upline ancestors)
     */
    private function getReferralChain(User $referrer): array
    {
        $ancestors = ReferralTree::ancestorsOf($referrer->id)
            ->with('user')
            ->get();

        return $ancestors->pluck('user')->filter()->all();
    }

    /**
     * Detect fraud patterns
     */
    private function detectPatterns(string $newIP, string $newFingerprint, array $chain): ?array
    {
        foreach ($chain as $chainUser) {
            // Get device data from location trait (stored during their registration)
            $chainDeviceData = json_decode($chainUser->device_fingerprint ?? '{}', true);

            // Check IP match
            if (isset($chainDeviceData['ip']) && $chainDeviceData['ip'] === $newIP) {
                return [
                    'type' => 'IP_MATCH',
                    'matched_user_id' => $chainUser->id,
                    'matched_user_name' => $chainUser->full_name,
                    'details' => "Same IP address: {$newIP}",
                ];
            }

            // Check device fingerprint match
            $chainFingerprint = $chainDeviceData['fingerprint'] ?? null;
            if ($chainFingerprint && $chainFingerprint === $newFingerprint) {
                return [
                    'type' => 'DEVICE_MATCH',
                    'matched_user_id' => $chainUser->id,
                    'matched_user_name' => $chainUser->full_name,
                    'details' => "Same device fingerprint detected",
                ];
            }

            // Check browser/OS pattern
            if ($this->isSimilarDevice($chainDeviceData, $this->extractLocationData())) {
                return [
                    'type' => 'PATTERN_ABUSE',
                    'matched_user_id' => $chainUser->id,
                    'matched_user_name' => $chainUser->full_name,
                    'details' => "Suspiciously similar device characteristics",
                ];
            }
        }

        return null;
    }

    /**
     * Handle fraud incident with progressive penalties
     */
    private function handleFraudIncident(User $newUser, User $referrer, array $fraudData, array $chain): array
    {
        // Check if user has previous fraud incidents
        $previousIncidents = FraudIncident::where('user_id', $newUser->id)
            ->orWhere('user_id', $referrer->id)
            ->count();

        // Progressive penalty
        if ($previousIncidents === 0) {
            // First offense: 2 days task ban
            $action = 'TASK_BANNED_2DAYS';
            $bannedUntil = now()->addDays(2);
            $severity = 'MEDIUM';
        } else {
            // Second+ offense: Freeze entire tree
            $action = 'TREE_FROZEN';
            $bannedUntil = null; // Indefinite until support resolves
            $severity = 'HIGH';
        }

        // Get affected user IDs (entire chain)
        $affectedUserIds = array_merge(
            [$newUser->id, $referrer->id],
            array_column($chain, 'id')
        );

        // Create fraud incident
        FraudIncident::create([
            'user_id' => $newUser->id,
            'incident_type' => $fraudData['type'],
            'severity' => $severity,
            'incident_data' => [
                'matched_user_id' => $fraudData['matched_user_id'],
                'matched_user_name' => $fraudData['matched_user_name'],
                'details' => $fraudData['details'],
                'referrer_id' => $referrer->id,
            ],
            'affected_users' => $affectedUserIds,
            'action_taken' => $action,
            'banned_until' => $bannedUntil,
        ]);

        // Apply penalty to affected users
        if ($action === 'TASK_BANNED_2DAYS') {
            User::whereIn('id', [$newUser->id, $referrer->id])
                ->update(['task_ban_until' => $bannedUntil]);
        } else {
            User::whereIn('id', $affectedUserIds)
                ->update(['status' => 'SUSPENDED', 'task_ban_until' => null]);
        }

        return [
            'fraud_detected' => true,
            'action' => $action,
            'banned_until' => $bannedUntil,
            'fraud_type' => $fraudData['type'],
            'matched_user' => $fraudData['matched_user_name'],
            'affected_count' => count($affectedUserIds),
        ];
    }

    /**
     * Generate device fingerprint
     */
    private function generateDeviceFingerprint(array $deviceData): string
    {
        $components = [
            $deviceData['device']['browser'] ?? 'unknown',
            $deviceData['device']['platform'] ?? 'unknown',
            $deviceData['device']['device'] ?? 'unknown',
        ];

        return md5(implode('|', $components));
    }

    /**
     * Check if devices are suspiciously similar
     */
    private function isSimilarDevice(array $device1, array $device2): bool
    {
        $browser1 = $device1['device']['browser'] ?? '';
        $browser2 = $device2['device']['browser'] ?? '';
        $platform1 = $device1['device']['platform'] ?? '';
        $platform2 = $device2['device']['platform'] ?? '';

        return $browser1 === $browser2 && $platform1 === $platform2;
    }

    /**
     * Check proxycheck.io (Free: 1,000 queries/day)
     */
    private function checkProxyCheckIo(string $ip): ?array
    {
        try {
            $response = Http::timeout(3)->get("https://proxycheck.io/v2/{$ip}?vpn=1&asn=1");

            if ($response->successful()) {
                $data = $response->json();
                $ipData = $data[$ip] ?? [];

                if (!empty($ipData)) {
                    return [
                        'is_vpn' => ($ipData['proxy'] ?? 'no') === 'yes',
                        'is_proxy' => ($ipData['proxy'] ?? 'no') === 'yes',
                        'is_tor' => ($ipData['type'] ?? '') === 'TOR',
                        'is_datacenter' => in_array($ipData['type'] ?? '', ['VPN', 'DCH']),
                        'threat_level' => ($ipData['proxy'] ?? 'no') === 'yes' ? 'HIGH' : 'LOW',
                        'isp' => $ipData['isp'] ?? null,
                        'organization' => $ipData['organisation'] ?? null,
                        'detection_method' => 'proxycheck.io',
                        'raw_data' => $ipData,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("ProxyCheck.io API failed for IP {$ip}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Check IPQualityScore (Free: 5,000 lookups/month)
     * Requires API key in .env: IPQUALITYSCORE_API_KEY
     */
    private function checkIpQualityScore(string $ip): ?array
    {
        $apiKey = config('services.ipqualityscore.key');
        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::timeout(3)->get("https://ipqualityscore.com/api/json/ip/{$apiKey}/{$ip}?strictness=1");

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success'] ?? false) {
                    return [
                        'is_vpn' => $data['vpn'] ?? false,
                        'is_proxy' => $data['proxy'] ?? false,
                        'is_tor' => $data['tor'] ?? false,
                        'is_datacenter' => $data['datacenter'] ?? false,
                        'threat_level' => $this->calculateThreatLevel($data['fraud_score'] ?? 0),
                        'isp' => $data['ISP'] ?? null,
                        'organization' => $data['organization'] ?? null,
                        'detection_method' => 'ipqualityscore',
                        'raw_data' => $data,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("IPQualityScore API failed for IP {$ip}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Check ip-api.com (Free: 45 requests/minute)
     * Limited VPN detection, mainly for geolocation
     */
    private function checkIpApiCom(string $ip): ?array
    {
        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=status,proxy,hosting,isp,org,country");

            if ($response->successful()) {
                $data = $response->json();

                if (($data['status'] ?? '') === 'success') {
                    return [
                        'is_vpn' => $data['proxy'] ?? false,
                        'is_proxy' => $data['proxy'] ?? false,
                        'is_tor' => false, // ip-api doesn't detect Tor
                        'is_datacenter' => $data['hosting'] ?? false,
                        'threat_level' => ($data['proxy'] || $data['hosting']) ? 'MEDIUM' : 'LOW',
                        'isp' => $data['isp'] ?? null,
                        'organization' => $data['org'] ?? null,
                        'detection_method' => 'ip-api.com',
                        'raw_data' => $data,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("IP-API.com failed for IP {$ip}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Check if IP is private/localhost
     */
    private function isPrivateIp(string $ip): bool
    {
        // Localhost
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }

        // Private IP ranges
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * Get IP type (IPv4 or IPv6)
     */
    private function getIpType(string $ip): string
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? 'IPv6' : 'IPv4';
    }

    /**
     * Calculate threat level from fraud score (0-100)
     */
    private function calculateThreatLevel(int $fraudScore): string
    {
        if ($fraudScore >= 85) return 'CRITICAL';
        if ($fraudScore >= 75) return 'HIGH';
        if ($fraudScore >= 50) return 'MEDIUM';
        return 'LOW';
    }
}
