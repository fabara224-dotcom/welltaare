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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    // Orange Money Web Payment (identifiants fournis par Orange Money Guinée)
    'orange_money' => [
        'enabled' => env('ORANGE_MONEY_ENABLED', true),
        'mode' => env('ORANGE_MONEY_MODE', 'sandbox'), // sandbox | live
        'authorization_header' => env('ORANGE_MONEY_AUTH_HEADER'), // "Basic xxxxx" fourni par Orange Developer
        'merchant_key' => env('ORANGE_MONEY_MERCHANT_KEY'),
        'token_url' => env('ORANGE_MONEY_TOKEN_URL', 'https://api.orange.com/oauth/v3/token'),
        'base_url' => env('ORANGE_MONEY_BASE_URL', 'https://api.orange.com/orange-money-webpay/dev/v1'),
        // En sandbox Orange utilise souvent la devise de test "OUV" ; en production "GNF"
        'currency' => env('ORANGE_MONEY_CURRENCY', 'OUV'),
        'lang' => 'fr',
        'reference' => env('ORANGE_MONEY_REFERENCE', 'WELLTAARE'),
    ],

];
