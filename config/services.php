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
        'token' => env('POSTMARK_TOKEN'),
    ],
    'mikrotik' => [
        'url' => env('MIKROTIK_URL', 'http://id-27.hostddns.us:30179'),
        'username' => env('MIKROTIK_USERNAME', 'agif'),
        'password' => env('MIKROTIK_PASSWORD', 'agif1705@gmail.com'),

    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'gowa' => [
        'base_url' => 'https://gowas.sikucur.com',
        'username' => 'sikucur',
        'password' => 'nagari-sikucur',
    ],

    'supabase' => [
        'url' => env('SUPABASE_URL', 'https://realtime.sikucur.com'),
        'anon_key' => env('SUPABASE_ANON_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJyb2xlIjoiYW5vbiIsImlzcyI6InN1cGFiYXNlIiwiaWF0IjoxNzU1NjIyODAwLCJleHAiOjE5MTMzODkyMDB9.mDUDIN37Evdb7-Rf-0DzN_JT2smCnnxR9EAyRyJZiF8'),
        'channel' => env('SUPABASE_REALTIME_CHANNEL', 'realtime_absensi_tv'),
    ],

];
