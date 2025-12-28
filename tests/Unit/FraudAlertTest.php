<?php

namespace Tests\Unit;

use App\Mail\FraudAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FraudAlertTest extends TestCase
{
    public function test_fraud_alert_renders_without_error()
    {
        $user = User::factory()->make([
            'id' => 'test-user-id',
            'full_name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $vpnData = [
            'is_vpn' => true,
            'is_proxy' => true,
            'threat_level' => 'HIGH',
            'isp' => 'Test ISP',
        ];

        $mailable = new FraudAlert(
            $user,
            'vpn_warning',
            'Test Title',
            'Test message content',
            $vpnData
        );

        $mailable->assertSeeInHtml('Test User');
        $mailable->assertSeeInHtml('Test message content');
    }
}
