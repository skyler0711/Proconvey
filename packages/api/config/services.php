<?php

use Illuminate\Support\Str;

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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'companies_house' => [
        'key' => env('COMPANIES_HOUSE_API_KEY'),
        'sandbox' => env('COMPANIES_HOUSE_SANDBOX'),
    ],

    'stripe' => [
        'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'client_id' => env('STRIPE_CLIENT_ID'),
        'products' => [
            'packs_seller' => env('STRIPE_PRODUCT_ID_PACKS_SELLER'),
            'packs_buyer' => env('STRIPE_PRODUCT_ID_PACKS_BUYER'),
            'packs_remortgage' => env('STRIPE_PRODUCT_ID_PACKS_REMORTGAGE'),

            'idv_seller' => env('STRIPE_PRODUCT_ID_IDV_SELLER'),
            'idv_buyer' => env('STRIPE_PRODUCT_ID_IDV_BUYER'),
            'idv_remortgage' => env('STRIPE_PRODUCT_ID_IDV_REMORTGAGE'),

            'esig_seller' => env('STRIPE_PRODUCT_ID_ESIG_SELLER'),
            'esig_buyer' => env('STRIPE_PRODUCT_ID_ESIG_BUYER'),
            'esig_remortgage' => env('STRIPE_PRODUCT_ID_ESIG_REMORTGAGE'),
        ],
    ],

    'yoti_sign' => [
        'key' => env('YOTI_SIGN_KEY'),
        'sandbox' => env('YOTI_SIGN_SANDBOX'),
    ],

    'yoti_idv' => [
        'sdk_id' => env('YOTI_IDV_SDK_ID'),
        'pem' => Str::replace('\n', "\n", env('YOTI_IDV_PEM')),
        'sandbox' => env('YOTI_IDV_SANDBOX'),
    ],

    'os_dh' => [
        'key' => env('OS_DH_KEY'),
    ],
];
