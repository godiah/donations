<?php

namespace App\Providers;

use App\Channels\SmsChannel;
use App\Channels\WhatsAppChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;

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
        $this->app->make(ChannelManager::class)->extend('sms', function ($app) {
            return new SmsChannel();
        });

        $this->app->make(ChannelManager::class)->extend('whatsapp', function ($app) {
            return new WhatsAppChannel();
        });
    }
}
