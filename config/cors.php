<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS','*'],

    'allowed_origins' => ['https://siadev.umuka.ac.id','https://sia.umuka.ac.id','*'], // Tambahkan domain pengirim request

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-Custom-Header', 'X-CSRF-TOKEN', 'Authorization','*'], // Pastikan X-CSRF-TOKEN ada di sini

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
