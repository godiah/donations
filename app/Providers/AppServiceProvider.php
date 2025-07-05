<?php

namespace App\Providers;

use App\Channels\SmsChannel;
use App\Channels\WhatsAppChannel;
use App\Services\CyberSourceService;
use App\Services\SmileIdentityService;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Smile Identity Service as singleton
        $this->app->singleton(SmileIdentityService::class, function ($app) {
            return new SmileIdentityService();
        });

        // Register CyberSource service as singleton
        $this->app->singleton(CyberSourceService::class, function ($app) {
            return new CyberSourceService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->make(ChannelManager::class)->extend('sms', function ($app) {
            return new SmsChannel();
        });

        $this->app->make(ChannelManager::class)->extend('whatsapp', function ($app) {
            return new WhatsAppChannel();
        });
    }
}
