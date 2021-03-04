<?php

namespace App\Providers;

use App\Services\SMSService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SMSServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(SMSService::class, function ($app) {
            return new SMSService(
                config('app.gateway_api_token')
            );
        });
    }

    public function provides()
    {
        return ['sms'];
    }

}
