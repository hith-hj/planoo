<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | The API only accepts cross-origin requests from explicitly trusted
    | origins. Configure them with the CORS_ALLOWED_ORIGINS environment
    | variable as a comma separated list (e.g. "https://app.planoo.sy").
    | When the variable is empty no origin is allowed by default.
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_filter(array_map(
        callback: 'trim',
        array: explode(',', (string) env('CORS_ALLOWED_ORIGINS', '')),
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Accept', 'Authorization', 'Content-Type', 'X-Requested-With', 'X-Language'],

    'exposed_headers' => [],

    'max_age' => 3600,

    'supports_credentials' => false,

];
