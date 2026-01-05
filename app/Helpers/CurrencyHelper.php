<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyHelper
{
    /**
     * Get USDT to local currency conversion rate
     * Uses CoinGecko API with optional caching
     *
     * @param string|null $currencyCode Currency code (e.g., 'ngn', 'usd', 'kes')
     * @param bool $noCache Set to true to bypass cache and get real-time rate
     * @return float|null Exchange rate or null if failed
     */
    public static function getUSDTRate(?string $currencyCode = null, bool $noCache = false): ?float
    {
        if (!$currencyCode) {
            $settings = \App\Models\GlobalSetting::first();
            $countryCode = $settings->country_of_operation ?? 'NGA';
            $currencyCode = CountryHelper::getCurrencyCode($countryCode);
        }

        $currencyCode = strtolower($currencyCode);
        $cacheKey = "usdt_rate_{$currencyCode}";

        // If noCache is true, fetch directly without caching
        if ($noCache) {
            return self::fetchUSDTRate($currencyCode);
        }

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($currencyCode) {
            return self::fetchUSDTRate($currencyCode);
        });
    }

    /**
     * Fetch USDT rate from API (internal method)
     */
    private static function fetchUSDTRate(string $currencyCode): ?float
    {
        try {
            // Try CoinGecko first
            $response = Http::timeout(10)->get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => 'tether',
                'vs_currencies' => $currencyCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['tether'][$currencyCode] ?? null;
            }

            // Fallback to Binance (if available)
            return self::getBinanceRate($currencyCode);
        } catch (\Exception $e) {
            \Log::error('Currency conversion failed', [
                'currency' => $currencyCode,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Fallback: Get rate from Binance API
     */
    private static function getBinanceRate(string $currencyCode): ?float
    {
        try {
            $symbol = 'USDT' . strtoupper($currencyCode);
            $response = Http::timeout(10)->get("https://api.binance.com/api/v3/ticker/price", [
                'symbol' => $symbol,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return (float) ($data['price'] ?? null);
            }
        } catch (\Exception $e) {
            \Log::error('Binance fallback failed', [
                'currency' => $currencyCode,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Convert amount from local currency to USDT
     *
     * @param float $amount Amount in local currency
     * @param string|null $currencyCode Currency code
     * @param bool $noCache Set to true to bypass cache and get real-time rate
     * @return float|null Equivalent in USDT
     */
    public static function toUSDT(float $amount, ?string $currencyCode = null, bool $noCache = false): ?float
    {
        $rate = self::getUSDTRate($currencyCode, $noCache);

        if (!$rate || $rate <= 0) {
            return null;
        }

        return $amount / $rate;
    }

    /**
     * Convert amount from USDT to local currency
     *
     * @param float $usdtAmount Amount in USDT
     * @param string|null $currencyCode Currency code
     * @return float|null Equivalent in local currency
     */
    public static function fromUSDT(float $usdtAmount, ?string $currencyCode = null): ?float
    {
        $rate = self::getUSDTRate($currencyCode);

        if (!$rate) {
            return null;
        }

        return $usdtAmount * $rate;
    }

    /**
     * Format crypto amount with proper decimals
     *
     * @param float $amount Crypto amount
     * @param int $decimals Number of decimal places (default: 6)
     * @return string Formatted amount
     */
    public static function formatCrypto(float $amount, int $decimals = 6): string
    {
        return number_format($amount, $decimals, '.', '');
    }

    /**
     * Get conversion display text
     * Example: "1,500,000 NGN ≈ 950.25 USDT"
     *
     * @param float $localAmount Amount in local currency
     * @param string|null $currencyCode Currency code
     * @return string Display text
     */
    public static function getConversionDisplay(float $localAmount, ?string $currencyCode = null): string
    {
        if (!$currencyCode) {
            $settings = \App\Models\GlobalSetting::first();
            $countryCode = $settings->country_of_operation ?? 'NGA';
            $currencyCode = CountryHelper::getCurrencyCode($countryCode);
        }

        $usdtAmount = self::toUSDT($localAmount, $currencyCode);

        if (!$usdtAmount) {
            return number_format($localAmount, 2) . ' ' . strtoupper($currencyCode);
        }

        $formattedLocal = number_format($localAmount, 2);
        $formattedUSDT = self::formatCrypto($usdtAmount, 2);

        return "{$formattedLocal} " . strtoupper($currencyCode) . " ≈ {$formattedUSDT} USDT";
    }
}
