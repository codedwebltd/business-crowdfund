<?php

namespace App\Helpers;

class CountryHelper
{
    private static ?array $countries = null;

    /**
     * Load countries data from JSON file
     */
    private static function loadCountries(): array
    {
        if (self::$countries === null) {
            $jsonPath = public_path('countries/world-countries.json');
            if (file_exists($jsonPath)) {
                self::$countries = json_decode(file_get_contents($jsonPath), true);
            } else {
                self::$countries = [];
            }
        }

        return self::$countries;
    }

    /**
     * Get country by ISO Alpha-2 code (e.g., 'NG', 'GH', 'KE')
     */
    public static function getByCode(string $code): ?array
    {
        $countries = self::loadCountries();

        foreach ($countries as $country) {
            if (strtoupper($country['isoAlpha2']) === strtoupper($code)) {
                return $country;
            }
        }

        return null;
    }

    /**
     * Get country by ISO Alpha-3 code (e.g., 'NGA', 'GHA', 'KEN')
     */
    public static function getByAlpha3(string $code): ?array
    {
        $countries = self::loadCountries();

        foreach ($countries as $country) {
            if (strtoupper($country['isoAlpha3']) === strtoupper($code)) {
                return $country;
            }
        }

        return null;
    }

    /**
     * Get currency info from country code
     * Returns: ['code' => 'NGN', 'name' => 'Naira', 'symbol' => '₦']
     */
    public static function getCurrency(string $countryCode): ?array
    {
        // Try Alpha-3 first (NGA, GHA), then Alpha-2 (NG, GH)
        $country = strlen($countryCode) === 3
            ? self::getByAlpha3($countryCode)
            : self::getByCode($countryCode);

        return $country['currency'] ?? null;
    }

    /**
     * Get currency symbol from country code
     */
    public static function getCurrencySymbol(string $countryCode): string
    {
        $currency = self::getCurrency($countryCode);
        return $currency['symbol'] ?? '$';
    }

    /**
     * Get currency code from country code (e.g., 'NGA' → 'NGN')
     */
    public static function getCurrencyCode(string $countryCode): string
    {
        $currency = self::getCurrency($countryCode);
        return $currency['code'] ?? 'USD';
    }

    /**
     * Get flag (base64 image) from country code
     */
    public static function getFlag(string $countryCode): ?string
    {
        $country = strlen($countryCode) === 3
            ? self::getByAlpha3($countryCode)
            : self::getByCode($countryCode);

        return $country['flag'] ?? null;
    }

    /**
     * Format amount with currency symbol
     * Usage: CountryHelper::formatMoney(5000, 'NGA') → '₦5,000.00'
     */
    public static function formatMoney(float $amount, ?string $countryCode = null): string
    {
        if (!$countryCode) {
            // Use platform default from global_settings
            $settings = \App\Models\GlobalSetting::first();
            $countryCode = $settings->country_of_operation ?? 'NGA';
        }

        $symbol = self::getCurrencySymbol($countryCode);
        return $symbol . number_format($amount, 2);
    }

    /**
     * Get all countries
     */
    public static function all(): array
    {
        return self::loadCountries();
    }

    /**
     * Get countries for dropdown (id, name, currency)
     */
    public static function forDropdown(): array
    {
        $countries = self::loadCountries();
        $result = [];

        foreach ($countries as $country) {
            $result[] = [
                'code' => $country['isoAlpha3'],
                'name' => $country['name'],
                'currency' => $country['currency']['code'] ?? 'USD',
                'symbol' => $country['currency']['symbol'] ?? '$',
                'flag' => $country['flag'] ?? null,
            ];
        }

        return $result;
    }
}
