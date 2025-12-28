<?php

namespace App\Services;

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;

class Google2FAService
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Generate secret key
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Generate QR code as SVG
     */
    public function generateQRCode(string $email, string $secret): string
    {
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        return $writer->writeString($qrCodeUrl);
    }

    /**
     * Verify 2FA code
     */
    public function verify(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code);
    }

    /**
     * Generate backup codes
     */
    public function generateBackupCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 4) . '-' .
                                  substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 4));
        }
        return $codes;
    }

    /**
     * Encrypt backup codes before storage
     */
    public function encryptBackupCodes(array $codes): array
    {
        return array_map(fn($code) => ['code' => Crypt::encryptString($code), 'used' => false], $codes);
    }

    /**
     * Decrypt backup codes
     */
    public function decryptBackupCodes(array $encryptedCodes): array
    {
        return array_map(fn($item) => [
            'code' => Crypt::decryptString($item['code']),
            'used' => $item['used']
        ], $encryptedCodes);
    }

    /**
     * Verify backup code
     */
    public function verifyBackupCode(array $encryptedCodes, string $inputCode): bool|int
    {
        foreach ($encryptedCodes as $index => $item) {
            if (!$item['used'] && Crypt::decryptString($item['code']) === strtoupper($inputCode)) {
                return $index; // Return index to mark as used
            }
        }
        return false;
    }
}
