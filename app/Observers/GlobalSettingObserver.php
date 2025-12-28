<?php

namespace App\Observers;

use App\Events\TokenRateUpdated;
use App\Jobs\NotifyUsersOfRateChange;
use App\Models\GlobalSetting;
use App\Models\TokenRateHistory;
use Illuminate\Support\Facades\Bus;

class GlobalSettingObserver
{
    /**
     * Handle the GlobalSetting "updated" event.
     */
    public function updated(GlobalSetting $setting): void
    {
        // Check if token-related settings changed
        $tokenSettingsChanged = $setting->isDirty('token_settings');
        $withdrawalRateChanged = $setting->isDirty('withdrawal_rate');

        if (!$tokenSettingsChanged && !$withdrawalRateChanged) {
            return; // No token changes, skip
        }

        // Only proceed if fluctuation is enabled
        if (!($setting->token_settings['fluctuation_enabled'] ?? false)) {
            return;
        }

        // Get previous values for comparison
        $oldTokenSettings = $setting->getOriginal('token_settings');
        $oldWithdrawalRate = $setting->getOriginal('withdrawal_rate');

        $newTokenPrice = $setting->token_settings['token_price'] ?? 850;
        $newWithdrawalRate = (float) $setting->withdrawal_rate;

        // Get previous history to calculate change
        $previousHistory = TokenRateHistory::latest()->first();

        $priceChange = 0;
        $rateChange = 0;
        $trend = 'stable';

        if ($previousHistory) {
            $priceChange = $newTokenPrice - $previousHistory->token_price;
            $rateChange = $newWithdrawalRate - $previousHistory->withdrawal_rate;

            // Determine trend (based on withdrawal rate - more important for users)
            if ($rateChange > 0.01) {
                $trend = 'up';
            } elseif ($rateChange < -0.01) {
                $trend = 'down';
            }
        }

        // Create history record
        $rateHistory = TokenRateHistory::create([
            'token_price' => $newTokenPrice,
            'withdrawal_rate' => $newWithdrawalRate,
            'price_change' => $priceChange,
            'rate_change' => $rateChange,
            'trend' => $trend,
            'changed_by' => auth()->id(),
            'change_reason' => null, // Admin can set this via form
        ]);

        // Broadcast to all connected users via WebSocket
        broadcast(new TokenRateUpdated($rateHistory))->toOthers();

        // Dispatch batched notification job (handles 10k+ users efficiently)
        Bus::dispatch(new NotifyUsersOfRateChange($rateHistory));
    }
}
