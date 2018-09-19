<?php

namespace FruitcakeStudio\GoogleCloudPrint;


use Google_Client;
use Illuminate\Support\Facades\Storage;

class CloudPrint
{
    private $googleHttpClient = null;

    public function acceptPrinter($printerId = null)
    {
        $httpClient = $this->getGoogleHttpClient();
        $httpClient->get('https://www.google.com/cloudprint/processinvite?printerid='.$this->getPrinterId($printerId).'&accept=true');

        return 'Complete';
    }

    public function printStoredFile($title, $filename, $printerId = null)
    {
        $content = 'data:'.Storage::mimeType($filename).';base64,' . base64_encode(Storage::get($filename));

        return $this->printData($title, $content, 'dataUrl', $this->getPrinterId($printerId));
    }

    public function printFile($title, $content, $mimeType, $printerId = null)
    {
        $content = 'data:'.$mimeType.';base64,' . base64_encode($content);

        return $this->printData($title, $content, 'dataUrl', $this->getPrinterId($printerId));
    }

    private function printData($title, $content, $contentType, $printerId)
    {
        $httpClient = $this->getGoogleHttpClient();

        $params = array(
            'printerid' => $printerId,
            'title' => $title,
            'ticket' => '{"version":"1.0","print":{}}',
            'content' => $content,
            'contentType' => $contentType
        );

        $response = $httpClient->post('https://www.google.com/cloudprint/submit', [
            'form_params' => $params,
        ]);

        return json_decode($response->getBody());
    }

    private function getPrinterId($printerId)
    {
        if(!$printerId = $printerId ?: config('cloudprint.printer_id', null)) {
            throw new \Exception('PrinterId not found');
        };

        return $printerId;
    }

    private function getGoogleHttpClient()
    {
        return $this->googleHttpClient ? : $this->createGoogleHttpClient();
    }

    private function createGoogleHttpClient()
    {
        $client = new Google_Client();
        $client->setAuthConfig(Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . config('cloudprint.auth_key_file'));
        $client->setApplicationName(config('cloudprint.app_name'));
        $client->setScopes(array('https://www.googleapis.com/auth/cloudprint'));

        return $client->authorize();
    }
}