<?php

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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google_pagespeed' => [
        'key' => env('GOOGLE_PAGESPEED_API_KEY'),
    ],

    'virustotal' => [
        'key' => env('VIRUSTOTAL_API_KEY'),
    ],

    'browsershot' => [
        'enabled' => env('BROWSERSHOT_ENABLED', true),
        'timeout' => (int) env('BROWSERSHOT_TIMEOUT', 30),
        'node_path' => env('BROWSERSHOT_NODE_PATH'),
        'npm_path' => env('BROWSERSHOT_NPM_PATH'),
        'chrome_path' => env('BROWSERSHOT_CHROME_PATH'),
    ],

    'screenshots' => [
        'provider' => env('SCREENSHOT_PROVIDER', 'browsershot'),
        'external_templates' => array_values(array_filter(array_map(
            'trim',
            explode(
                ',',
                env(
                    'SCREENSHOT_EXTERNAL_TEMPLATES',
                    'https://s.wordpress.com/mshots/v1/{url}?w=1366'
                )
            )
        ))),
        'external_timeout' => (int) env('SCREENSHOT_EXTERNAL_TIMEOUT', 15),
        'verify_ssl' => env('SCREENSHOT_VERIFY_SSL', false),
    ],

];
