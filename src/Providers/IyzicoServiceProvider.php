<?php

namespace Aghaeian\Iyzico\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Iyzico service provider
 *
 * @author  aghae
 */
class IyzicoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'iyzico');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'iyzico');

        $this->publishes([
            __DIR__ . '/../Resources/assets' => public_path('vendor/aghaeian/iyzico/assets'),
        ], 'iyzico');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        // Ödeme yöntemi yapılandırmasını birleştirir
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php',
            'payment_methods'
        );

        // Sistem yapılandırmasını birleştirir
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php',
            'core'
        );
    }
}
