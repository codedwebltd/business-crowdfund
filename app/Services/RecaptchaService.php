<?php

namespace App\Services;

use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    protected $settings;

    public function __construct()
    {
        $this->settings = null;
    }

    protected function getSettings()
    {
        if ($this->settings === null) {
            $this->settings = GlobalSetting::first();
        }
        return $this->settings;
    }

    public function verify(string $token): bool
    {
        $settings = $this->getSettings();

        if (!$settings->recaptcha_enabled) {
            return true; // Skip if disabled
        }

        if (!$settings->recaptcha_secret_key) {
            logger()->warning('reCAPTCHA secret key not configured');
            return true; // Don't block if not configured
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $settings->recaptcha_secret_key,
                'response' => $token
            ]);

            $result = $response->json();

            return $result['success'] ?? false;
        } catch (\Exception $e) {
            logger()->error('reCAPTCHA verification failed: ' . $e->getMessage());
            return true; // Don't block on API failure
        }
    }

    public function isEnabled(): bool
    {
        return $this->getSettings()->recaptcha_enabled ?? false;
    }

    public function getSiteKey(): ?string
    {
        return $this->getSettings()->recaptcha_site_key;
    }

    public function shouldTriggerOnFraud(): bool
    {
        return $this->getSettings()->recaptcha_trigger_on_fraud ?? true;
    }
}
