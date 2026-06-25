<?php

namespace Foutraz\Outlook\Providers;

use Foutraz\Outlook\OutlookManager;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class OutlookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__, 2).'/config/outlook.php',
            'outlook'
        );

        $this->app->singleton(OutlookManager::class, function ($app) {

            $config = $app['config']['outlook'];

            if (blank($config['endpoint'])) {
                throw new RuntimeException(
                    'No Outlook API endpoint was provided.'
                );
            }

            return new OutlookManager(
                $config['endpoint'],
                $config['token'],
                $config['client_id'],
                $config['client_secret'],
                $config['redirect_uri'],
                $config['tenant'],
            );
        });

        $this->app->alias(OutlookManager::class, 'outlook');
    }

    public function boot(): void
    {
        $this->publishes([
            dirname(__DIR__, 2).'/config/outlook.php' =>
                $this->app->configPath('outlook.php'),
        ], 'outlook-config');
    }
}
