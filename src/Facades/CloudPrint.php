<?php

namespace FruitcakeStudio\GoogleCloudPrint\Facades;

use Illuminate\Support\Facades\Facade;

class CloudPrint extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cloudprint';
    }
}