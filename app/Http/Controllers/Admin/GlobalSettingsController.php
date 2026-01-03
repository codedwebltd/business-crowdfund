<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\Plan;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class GlobalSettingsController extends Controller
{
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo_file' => 'required|image|max:2048'
        ]);

        $uploadService = app(FileUploadService::class);
        $result = $uploadService->uploadFile($request->file('logo_file'), 'settings/logos');

        return response()->json($result);
    }

    public function updateAppConfig(Request $request)
    {
        $settings = GlobalSetting::first();
        $settings->update($request->only([
            'app_name', 'app_url', 'country_of_operation', 'platform_currency',
            'app_description', 'daily_earning_average', 'time_required',
            'anonymity_level', 'total_users', 'site_logo'
        ]));
        return back();
    }

    public function updateReferral(Request $request)
    {
        $settings = GlobalSetting::first();

        // Handle dynamic referral levels
        $depth = $request->referral_levels_depth;
        $activation = [];
        $taskEarnings = [];

        // Build activation commissions up to depth
        for ($i = 1; $i <= $depth; $i++) {
            $activation[$i] = $request->input("commission_rates.activation.{$i}", 0);
        }

        // Build task earnings commissions (usually shorter)
        for ($i = 1; $i <= min($depth, 10); $i++) {
            $taskEarnings[$i] = $request->input("commission_rates.task_earnings.{$i}", 0);
        }

        $settings->update([
            'referral_levels_depth' => $depth,
            'commission_rates' => [
                'activation' => $activation,
                'task_earnings' => $taskEarnings
            ]
        ]);

        return back();
    }

    public function updateTasks(Request $request)
    {
        $settings = GlobalSetting::first();

        // Get plan names dynamically from database
        $plans = Plan::pluck('name', 'id');
        $dailyLimits = [];

        foreach ($plans as $id => $name) {
            $key = strtolower($name);
            $dailyLimits[$key] = $request->input("daily_task_limits.{$key}", 0);
        }

        $settings->update([
            'task_distribution_percentages' => $request->task_distribution_percentages,
            'daily_task_limits' => $dailyLimits,
            'task_validation_rules' => $request->task_validation_rules
        ]);

        return back();
    }

    public function updateRanks(Request $request)
    {
        GlobalSetting::first()->update($request->only([
            'rank_criteria',
            'rank_commission_multipliers',
            'diamond_leadership_bonus',
            'withdrawal_processing_times'
        ]));
        return back();
    }

    public function updateFinancial(Request $request)
    {
        GlobalSetting::first()->update($request->only([
            'withdrawal_limits_by_rank',
            'payment_gateways',
            'bank_accounts',
            'crypto_wallets',
            'minimum_withdrawal',
            'maximum_withdrawal',
            'withdrawals_per_day',
            'withdrawal_processing_times'
        ]));
        return back();
    }

    public function updateFraud(Request $request)
    {
        GlobalSetting::first()->update($request->only(['fraud_detection_rules']));
        return back();
    }

    public function updateWithdrawals(Request $request)
    {
        GlobalSetting::first()->update($request->only([
            'withdrawal_rate',
            'total_members',
            'liquidity_settings'
        ]));
        return back();
    }

    public function updateToken(Request $request)
    {
        GlobalSetting::first()->update($request->only([
            'token_settings',
            'withdrawal_rate',
            'liquidity_settings',
            'pending_balance_maturation_hours'
        ]));
        return back();
    }

    public function updateKYC(Request $request)
    {
        GlobalSetting::first()->update($request->only([
            'kyc_requirements',
            'kyc_withdrawal_threshold',
            'enable_kyc_on_first_withdrawal',
            'require_testimonial_first_withdrawal'
        ]));
        return back();
    }

    public function updateSystem(Request $request)
    {
        GlobalSetting::first()->update($request->only([
            'maintenance_mode',
            'new_registrations_enabled',
            'withdrawals_enabled',
            'referral_bonuses_enabled',
            'total_members'
        ]));
        return back();
    }

    public function updateNotifications(Request $request)
    {
        GlobalSetting::first()->update($request->only([
            'notification_channels',
            'email_notifications_enabled',
            'sms_notifications_enabled'
        ]));
        return back();
    }

    public function updateIntegrations(Request $request)
    {
        GlobalSetting::first()->update($request->only([
            'mail_mailer', 'mail_host', 'mail_port',
            'mail_username', 'mail_password', 'mail_encryption',
            'mail_from_address', 'mail_from_name',
            'pusher_app_id', 'pusher_app_key', 'pusher_app_secret', 'pusher_app_cluster',
            'support_email', 'support_phone', 'support_whatsapp'
        ]));
        return back();
    }

    public function updateAI(Request $request)
    {
        GlobalSetting::first()->update($request->only([
            'ai_task_generation_enabled',
            'ai_configuration',
            'ai_generation_frequency_hours',
            'min_task_templates_threshold'
        ]));
        return back();
    }

    public function updateSecurity(Request $request)
    {
        GlobalSetting::first()->update($request->only([
            'recaptcha_site_key',
            'recaptcha_secret_key',
            'recaptcha_enabled',
            'recaptcha_trigger_on_fraud'
        ]));
        return back();
    }
}
