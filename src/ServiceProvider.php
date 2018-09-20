<?php

namespace FruitcakeStudio\GoogleCloudPrint;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
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


        $this->app->singleton('CloudPrint', function ($app) {
            $manager = $app->make(CloudPrint::class);
            return $manager;
        });
    }
}