<?php

namespace FruitcakeStudio\GoogleCloudPrint;


use FruitcakeStudio\GoogleCloudPrint\Models\Printer;
use Google_Client;
use FruitcakeStudio\GoogleCloudPrint\Exceptions\PrinterNotFoundException;
use Illuminate\Support\Facades\Storage;

class CloudPrint
{
    private $googleHttpClient = null;

    /**
     * Get a printer instance
     *
     * @param $printerId
     * @return Printer
     * @throws PrinterNotFoundException
     */
    public function printer($printerId = null)
    {
        return new Printer($this, $printerId);
    }

    /**
     * Returns the GoogleClient HttpClient Instance
     *
     * @return \GuzzleHttp\ClientInterface
     * @throws \Google_Exception
     */
    public function getGoogleHttpClient()
    {
        return $this->googleHttpClient ? : $this->createGoogleHttpClient();
    }

    /**
     * Create a new GoogleClient Instance
     *
     * @return \GuzzleHttp\ClientInterface
     * @throws \Google_Exception
     */
    private function createGoogleHttpClient()
    {
        $client = new Google_Client();
        $client->setAuthConfig(Storage::disk(config('cloudprint.storage_driver'))->getDriver()->getAdapter()->getPathPrefix() . config('cloudprint.auth_key_file'));
        $client->setApplicationName(config('cloudprint.app_name'));
        $client->setScopes(['https://www.googleapis.com/auth/cloudprint']);

        return $client->authorize();
    }
}