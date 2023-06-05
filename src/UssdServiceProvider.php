<?php

namespace Stilinski\Ussd;

use Illuminate\Support\ServiceProvider;

class UssdServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'stilinski');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->mergeConfigFrom(__DIR__ . '/../config/ussd.php', 'ussd');
        $this->publishes([
            __DIR__ . '/resources/assets' => public_path('assets'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->publishResources();
        }
    }

    protected function publishResources()
    {
        $this->publishes([
            __DIR__ . '/resources/lang' => resource_path('lang'),
        ], 'ussd-lang');

        $this->publishes([
            __DIR__ . '/Repositories/Handler.php' => app_path('Repositories/Handler.php')
        ], 'ussd-repositories');
    }
}