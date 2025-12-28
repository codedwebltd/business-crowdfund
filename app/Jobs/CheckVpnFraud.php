<?php

namespace App\Jobs;

use App\Events\FraudLogout;
use App\Models\FraudIncident;
use App\Models\User;
use App\Services\FraudDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckVpnFraud implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $ipAddress;
    public $route;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public $timeout = 30;

    /**
     * The number of seconds to wait before retrying.
     */
    public $backoff = 10;

    /**
     * Delay before executing (5 seconds after login)
     */
    //public $delay = 5;

    public function __construct(User $user, string $ipAddress, string $route = 'login')
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress;
        $this->route = $route;

        // Queue with 5 second delay
        $this->delay = now()->addSeconds(5);
    }

    public function handle(FraudDetectionService $fraudService): void
    {
        try {
            // Detect VPN/Proxy
            $vpnData = $fraudService->detectVpnProxy($this->ipAddress);

            if ($vpnData['is_vpn'] || $vpnData['is_proxy'] || $vpnData['is_tor']) {

                Log::warning('⚠️ VPN DETECTED ASYNC', [
                    'user_id' => $this->user->id,
                    'user_name' => $this->user->full_name,
                    'ip' => $this->ipAddress,
                    'route' => $this->route,
                    'vpn_data' => $vpnData,
                ]);

                // Count previous VPN offenses
                $offenseCount = FraudIncident::where('user_id', $this->user->id)
                    ->where('incident_type', 'VPN_DETECTED')
                    ->count();

                $action = $this->determineAction($offenseCount);

                // Create fraud incident
                $incident = FraudIncident::create([
                    'user_id' => $this->user->id,
                    'incident_type' => 'VPN_DETECTED',
                    'severity' => $action['severity'],
                    'incident_data' => [
                        'ip_address' => $this->ipAddress,
                        'route' => $this->route,
                        'vpn_data' => $vpnData,
                        'offense_number' => $offenseCount + 1,
                    ],
                    'affected_users' => [$this->user->id],
                    'action_taken' => $action['action'],
                    'banned_until' => $action['banned_until'],
                ]);

                // Apply action
                $this->applyAction($action, $vpnData);

            }

        } catch (\Exception $e) {
            Log::error('VPN fraud check failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function determineAction(int $offenseCount): array
    {
        if ($offenseCount === 0) {
            // First offense: Warning
            return [
                'action' => 'WARNING_SENT',
                'severity' => 'LOW',
                'banned_until' => null,
            ];
        } elseif ($offenseCount === 1) {
            // Second offense: 48-hour task suspension
            return [
                'action' => 'TASK_SUSPENDED_48H',
                'severity' => 'MEDIUM',
                'banned_until' => now()->addHours(48),
            ];
        } else {
            // Third+ offense: 7-day ban
            return [
                'action' => 'BANNED_7DAYS',
                'severity' => 'HIGH',
                'banned_until' => now()->addDays(7),
            ];
        }
    }

    private function applyAction(array $action, array $vpnData): void
{
    if ($action['action'] === 'WARNING_SENT') {

        // Send warning email
        Mail::to($this->user->email)->send(new \App\Mail\FraudAlert(
            $this->user,
            'vpn_warning',
            'VPN/Proxy Detected - First Warning',
            "We detected that you logged in using a VPN or Proxy from {$this->ipAddress}. This is your first warning. Please use your real IP address to avoid account restrictions.",
            $vpnData
        ));

        // Broadcast Pusher event for instant logout
        Log::info('📡 Broadcasting FraudLogout event', ['user_id' => $this->user->id]);
        broadcast(new FraudLogout($this->user->id, [
            'title' => 'VPN/Proxy Detected',
            'message' => 'We detected VPN usage on your account. This is your first warning. You will be logged out for security.',
            'offense' => 1,
        ]))->toOthers();
        Log::info('✓ FraudLogout event broadcasted');

    } elseif ($action['action'] === 'TASK_SUSPENDED_48H') {

        // Suspend tasks for 48 hours
        $this->user->update([
            'task_ban_until' => $action['banned_until'],
        ]);

        // Send suspension email
        Mail::to($this->user->email)->send(new \App\Mail\FraudAlert(
            $this->user,
            'vpn_suspension',
            'Account Task Suspension - Second Warning',
            "This is your second VPN detection. Your task access has been suspended for 48 hours until {$action['banned_until']->format('M d, Y H:i')}.",
            $vpnData
        ));

        // Broadcast logout event
        broadcast(new FraudLogout($this->user->id, [
            'title' => 'Task Access Suspended',
            'message' => 'Second VPN detection. Task access suspended for 48 hours. You will be logged out.',
            'offense' => 2,
            'banned_until' => $action['banned_until']->toIso8601String(),
        ]));

    } else {

        // Ban for 7 days
        $this->user->update([
            'status' => 'SUSPENDED',
            'task_ban_until' => $action['banned_until'],
        ]);

        // Send ban email
        Mail::to($this->user->email)->send(new \App\Mail\FraudAlert(
            $this->user,
            'vpn_ban',
            'Account Suspended - Final Warning',
            "Your account has been suspended for 7 days until {$action['banned_until']->format('M d, Y H:i')} due to repeated VPN usage. This requires manual admin review.",
            $vpnData
        ));

        // Broadcast logout event
        broadcast(new FraudLogout($this->user->id, [
            'title' => 'Account Suspended',
            'message' => 'Your account has been suspended for 7 days due to repeated VPN usage.',
            'offense' => 3,
            'banned_until' => $action['banned_until']->toIso8601String(),
        ]));
    }
}
    public function failed(\Throwable $exception): void
    {
        Log::error('CheckVpnFraud job failed', [
            'user_id' => $this->user->id,
            'ip' => $this->ipAddress,
            'error' => $exception->getMessage(),
        ]);
    }
}
