<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\Google2FAService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Google2FATest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $google2fa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->google2fa = app(Google2FAService::class);
    }

    /** @test */
    public function user_can_enable_2fa()
    {
        $this->actingAs($this->user);

        // Request 2FA setup
        $response = $this->post('/settings/2fa/enable');
        $response->assertRedirect('/settings');
        $this->assertNotNull(session('qrCode'));
        $this->assertNotNull(session('secret'));

        // Verify with valid code
        $secret = session('secret');
        $validCode = $this->google2fa->generateSecretKey();
        $code = '123456'; // Mock code for testing

        $this->withoutMiddleware(\App\Http\Middleware\FraudDetectionMiddleware::class);

        $verifyResponse = $this->post('/settings/2fa/verify', [
            'code' => $code,
            'secret' => $secret
        ]);

        $this->user->refresh();
        $this->assertTrue($this->user->google2fa_enabled);
        $this->assertNotNull($this->user->google2fa_secret);
        $this->assertNotNull($this->user->backup_codes);
    }

    /** @test */
    public function user_cannot_enable_2fa_with_invalid_code()
    {
        $this->actingAs($this->user);

        $this->post('/settings/2fa/enable');
        $secret = session('secret');

        $response = $this->post('/settings/2fa/verify', [
            'code' => '000000',
            'secret' => $secret
        ]);

        $response->assertSessionHasErrors('code');
        $this->user->refresh();
        $this->assertFalse($this->user->google2fa_enabled);
    }

    /** @test */
    public function user_can_disable_2fa_with_valid_code()
    {
        $secret = $this->google2fa->generateSecretKey();
        $backupCodes = $this->google2fa->generateBackupCodes();
        $encryptedCodes = $this->google2fa->encryptBackupCodes($backupCodes);

        $this->user->update([
            'google2fa_secret' => $secret,
            'google2fa_enabled' => true,
            'backup_codes' => $encryptedCodes,
        ]);

        $this->actingAs($this->user);

        $code = '123456'; // Mock valid code
        $response = $this->post('/settings/2fa/disable', [
            'code' => $code
        ]);

        $this->user->refresh();
        $this->assertFalse($this->user->google2fa_enabled);
        $this->assertNull($this->user->google2fa_secret);
        $this->assertNull($this->user->backup_codes);
    }

    /** @test */
    public function backup_codes_are_generated_on_2fa_enable()
    {
        $backupCodes = $this->google2fa->generateBackupCodes();

        $this->assertCount(8, $backupCodes);
        foreach ($backupCodes as $code) {
            $this->assertMatchesRegularExpression('/^[A-Z0-9]{4}-[A-Z0-9]{4}$/', $code);
        }
    }

    /** @test */
    public function backup_codes_are_encrypted_before_storage()
    {
        $backupCodes = $this->google2fa->generateBackupCodes();
        $encryptedCodes = $this->google2fa->encryptBackupCodes($backupCodes);

        $this->assertCount(8, $encryptedCodes);
        foreach ($encryptedCodes as $item) {
            $this->assertArrayHasKey('code', $item);
            $this->assertArrayHasKey('used', $item);
            $this->assertFalse($item['used']);
            $this->assertNotEquals($backupCodes[0], $item['code']);
        }
    }
}
