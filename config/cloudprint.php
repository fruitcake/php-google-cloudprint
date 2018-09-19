<?php

return array(
    /**
     * Google cloudprint ID
     */
    'printer_id' => env('CLOUDPRINT_PRINTER_ID', ''),

    /**
     * Auth Key file (json) location
     *
     * By default should be located on storage/app
     */
    'auth_key_file' => env('CLOUDPRINT_AUTH_FILE', ''),

    /**
     * Cloudprint app name
     */
    'app_name' => env('CLOUDPRINT_APP_NAME', ''),
);