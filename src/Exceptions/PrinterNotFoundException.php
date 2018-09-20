<?php

namespace FruitcakeStudio\GoogleCloudPrint\Exceptions;

use Exception;

class PrinterNotFoundException extends Exception
{
    public function __construct(string $message = "")
    {
        parent::__construct($message, 500);
    }
}