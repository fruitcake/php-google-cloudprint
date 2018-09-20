<?php

namespace FruitcakeStudio\GoogleCloudPrint\Models;

use FruitcakeStudio\GoogleCloudPrint\CloudPrint;
use FruitcakeStudio\GoogleCloudPrint\Exceptions\PrinterNotFoundException;
use Illuminate\Support\Facades\Storage;

class Printer
{
    /**
     * @var string
     */
    private $printerId;

    /**
     * @var CloudPrint
     */
    private $cloudPrint;


    /**
     * Printer constructor.
     * @param CloudPrint $cloudPrint
     * @param string $printerId
     * @throws PrinterNotFoundException
     */
    public function __construct(CloudPrint $cloudPrint, $printerId = null)
    {
        if(!$printerId = $printerId ? : config('cloudprint.printer_id')) {
            throw new PrinterNotFoundException("No printerId given.");
        };

        $this->printerId = $printerId;
        $this->cloudPrint = $cloudPrint;
    }


    /**
     * Accept a share invite for a given printerId.
     * Uses config defined printerId if none is given.
     *
     * @param null $printerId
     * @return bool
     * @throws \Exception
     */
    public function acceptPrinter()
    {
        $httpClient = $this->cloudPrint->getGoogleHttpClient();
        $res = $httpClient->get('https://www.google.com/cloudprint/processinvite?printerid='.$this->printerId.'&accept=true');

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
     * @throws \Google_Exception
     */
    private function printData($jobName, $content, $contentType, $printerId)
    {
        $params = [
            'printerid' => $printerId,
            'title' => $jobName,
            'ticket' => '{"version":"1.0","print":{}}',
            'content' => $content,
            'contentType' => $contentType
        ];

        $response = $this->cloudPrint->getGoogleHttpClient()->post('https://www.google.com/cloudprint/submit', [
            'form_params' => $params,
        ]);

        return json_decode($response->getBody());
    }

    /**
     * @return string
     */
    public function getPrinterId(): string
    {
        return $this->printerId;
    }
}