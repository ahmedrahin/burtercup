<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SystemSetting;
use App\Models\SocialLink;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $settings = SystemSetting::first();
        $social   = SocialLink::first();
        view()->share([
            'system_settings' => $settings,
            'social' => $social
        ]);
    }
}
