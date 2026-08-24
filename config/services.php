<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'whatsapp' => [
        'token' => env('WHATSAPP_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'version' => env('WHATSAPP_VERSION', 'v20.0'),
    ],

    'syriatel' => [
        'user_name' => env('SYRIATEL_USER_NAME', 'PlanooApp1'),
        'password' => env('SYRIATEL_PASSWORD'),
        'sender' => env('SYRIATEL_SENDER', 'PlanooApp'),
        'webhook_url' => env('SYRIATEL_WEBHOOK_URL', 'http://planoo.sy/webhooks.php'),
        'webhook_secret' => env('SYRIATEL_WEBHOOK_SECRET', 'PlanooApp1_webhook_secret'),
    ],

    'conflicts_detection' => env('CONFLICTS_DETECTION', false),
];
