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

    // config('services.sparkpost.secret')를 사용하여 설정 값에 접근 가능
    'spartpost' => [
        'secret' => 'abcdefg',
    ],

    'bugsnag' => [
        'api_key' => env('BUGSNAG_API_KEY'),
    ],

];
