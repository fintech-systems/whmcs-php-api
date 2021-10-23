<?php

namespace FintechSystems\Whmcs;

use Illuminate\Support\ServiceProvider;

class WhmcsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/whmcs.php' => config_path('whmcs.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/whmcs.php', 'whmcs'
        );

        $this->app->bind('whmcs-api', function () {
            return new Whmcs([                
                'url'            => config('whmcs.url'),
                'api_identifier' => config('whmcs.api_identifier'),
                'api_secret'     => config('whmcs.api_secret'),
            ]);
        });
    }
}
