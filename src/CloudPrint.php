<?php

namespace FruitcakeStudio\GoogleCloudPrint;


use Google_Client;
use FruitcakeStudio\GoogleCloudPrint\Exceptions\PrinterNotFoundException;
use Illuminate\Support\Facades\Storage;

class CloudPrint
{
    private $googleHttpClient = null;

    /**
     * Accept a share invite for a given printerId.
     * Uses config defined printerId if none is given.
     *
     * @param null $printerId
     * @return bool
     * @throws \Exception
     */
    public function acceptPrinter($printerId = null)
    {
        $httpClient = $this->getGoogleHttpClient();
        $res = $httpClient->get('https://www.google.com/cloudprint/processinvite?printerid='.$this->getPrinterId($printerId).'&accept=true');

        return $res->getStatusCode() === 200;
    }

    /**
     * Print a file stored on the configured storage driver with the given printerId.
     * Uses config defined printerId if none is given.
     * Doesn't validate if the given file is printable.
     *
     * @param $jobName
     * @param $filename
     * @param null $printerId
     * @return array
     * @throws \Exception
     */
    public function printStoredFile($jobName, $filename, $printerId = null)
    {
        $content = 'data:'.Storage::disk(config('cloudprint.storage_driver'))->mimeType($filename).';base64,' . base64_encode(Storage::disk(config('cloudprint.storage_driver'))->get($filename));

        return $this->printData($jobName, $content, 'dataUrl', $this->getPrinterId($printerId));
    }

    /**
     * Prints a file stored in memory on the given printer Id.
     * Uses config defined printerId if none is given.
     * Doesn't validate if the given file is printable.
     *
     * @param $jobName
     * @param $content
     * @param $mimeType
     * @param null $printerId
     * @return array
     * @throws \Exception
     */
    public function printFile($jobName, $content, $mimeType, $printerId = null)
    {
        $content = 'data:'.$mimeType.';base64,' . base64_encode($content);

        return $this->printData($jobName, $content, 'dataUrl', $this->getPrinterId($printerId));
    }

    /**
     * Send the actual print request to google.
     *
     * @param $jobName
     * @param $content
     * @param $contentType
     * @param $printerId
     * @return array
     */
    private function printData($jobName, $content, $contentType, $printerId)
    {
        $httpClient = $this->getGoogleHttpClient();

        $params = [
            'printerid' => $printerId,
            'title' => $jobName,
            'ticket' => '{"version":"1.0","print":{}}',
            'content' => $content,
            'contentType' => $contentType
        ];

        $response = $httpClient->post('https://www.google.com/cloudprint/submit', [
            'form_params' => $params,
        ]);

        return json_decode($response->getBody());
    }

    /**
     * Return the printerId to use. Defaults to config if none is given.
     *
     * @param $printerId
     * @return \Illuminate\Config\Repository|mixed
     * @throws \Exception
     */
    private function getPrinterId($printerId)
    {
        if(!$printerId = $printerId ? : config('cloudprint.printer_id')) {
            throw new PrinterNotFoundException("No usable printerId found.");
        };

        return $printerId;
    }

    /**
     * Returns the GoogleClient HttpClient Instance
     *
     * @return \GuzzleHttp\ClientInterface
     * @throws \Google_Exception
     */
    private function getGoogleHttpClient()
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