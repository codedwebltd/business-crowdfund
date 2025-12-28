<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use App\Models\GlobalSetting;
use App\Models\User;
use App\Models\Withdrawal;
use App\Observers\GlobalSettingObserver;
use App\Observers\UserObserver;
use App\Observers\WithdrawalObserver;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load global settings and share with all views
        try {
            $globalSettings = GlobalSetting::get();

            // Share with Blade views
            View::share('globalSettings', $globalSettings);

            // Share with Inertia (Vue components)
            Inertia::share([
                'globalSettings' => fn () => $globalSettings,
                'appName' => fn () => $globalSettings->app_name ?? config('app.name'),
            ]);

            // Apply mail config from database (override .env)
            if ($globalSettings->mail_host) {
                Config::set('mail.mailers.smtp.host', $globalSettings->mail_host);
                Config::set('mail.mailers.smtp.port', $globalSettings->mail_port);
                Config::set('mail.mailers.smtp.username', $globalSettings->mail_username);
                Config::set('mail.mailers.smtp.password', $globalSettings->mail_password);
                Config::set('mail.mailers.smtp.encryption', $globalSettings->mail_encryption);
                Config::set('mail.from.address', $globalSettings->mail_from_address);
                Config::set('mail.from.name', $globalSettings->mail_from_name);
            }

            // Apply app config from database (override .env)
            if ($globalSettings->app_name) {
                Config::set('app.name', $globalSettings->app_name);
            }
            if ($globalSettings->app_url) {
                Config::set('app.url', $globalSettings->app_url);
            }

            // Apply Pusher config from database (override .env)
            if ($globalSettings->pusher_app_key) {
                Config::set('broadcasting.connections.pusher.key', $globalSettings->pusher_app_key);
                Config::set('broadcasting.connections.pusher.secret', $globalSettings->pusher_app_secret);
                Config::set('broadcasting.connections.pusher.app_id', $globalSettings->pusher_app_id);
                Config::set('broadcasting.connections.pusher.options.cluster', $globalSettings->pusher_app_cluster);
            }
        } catch (\Exception $e) {
            // Fail silently if table doesn't exist yet (during migration)
        }

        // Register model observers
        User::observe(UserObserver::class);
        Withdrawal::observe(WithdrawalObserver::class);
        GlobalSetting::observe(GlobalSettingObserver::class);
    }
}
