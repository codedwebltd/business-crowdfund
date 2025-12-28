<?php

namespace Tests\Feature;

use App\Events\FraudLogout;
use App\Jobs\CheckVpnFraud;
use App\Models\User;
use App\Services\FraudDetectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class VpnFraudBroadcastTest extends TestCase
{
    public function test_fraud_logout_event_is_broadcasted_on_vpn_detection()
    {
        Event::fake();

        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found in database');
        }

        // Mock VPN detection to return VPN detected
        $this->mock(FraudDetectionService::class, function ($mock) {
            $mock->shouldReceive('detectVpnProxy')
                ->once()
                ->andReturn([
                    'is_vpn' => true,
                    'is_proxy' => true,
                    'is_tor' => false,
                    'is_datacenter' => true,
                    'threat_level' => 'HIGH',
                    'isp' => null,
                    'organization' => 'Test VPN',
                ]);
        });

        // Dispatch the job
        $job = new CheckVpnFraud($user, '1.2.3.4', 'login');
        $job->handle(app(FraudDetectionService::class));

        // Assert FraudLogout event was dispatched
        Event::assertDispatched(FraudLogout::class, function ($event) use ($user) {
            return $event->userId === $user->id &&
                   $event->fraudData['offense'] === 1 &&
                   $event->fraudData['title'] === 'VPN/Proxy Detected';
        });
    }
}
