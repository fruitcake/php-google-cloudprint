<?php

namespace FruitcakeStudio\GoogleCloudPrint;

use Illuminate\Support\ServiceProvider;

class CloudPrintServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__.'/../config/cloudprint.php';
        $this->mergeConfigFrom($configPath, 'cloudprint');
        $this->publishes([$configPath => config_path('cloudprint.php')], 'config');


        $this->app->singleton('cloudprint', function ($app) {
            $manager = $app->make('FruitcakeStudio\GoogleCloudPrint\CloudPrint');
            return $manager;
        });
    }

    public function boot()
    {

    }
}