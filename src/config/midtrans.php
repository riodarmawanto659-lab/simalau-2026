<?php

$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN);

return [
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'server_key' => env('MIDTRANS_SERVER_KEY'),

    'is_production' => $isProduction,

    'sandbox_snap_api_url' => 'https://app.sandbox.midtrans.com/snap/v1/transactions',
    'production_snap_api_url' => 'https://app.midtrans.com/snap/v1/transactions',

    'sandbox_snap_js_url' => 'https://app.sandbox.midtrans.com/snap/snap.js',
    'production_snap_js_url' => 'https://app.midtrans.com/snap/snap.js',

    'snap_api_url' => $isProduction
        ? 'https://app.midtrans.com/snap/v1/transactions'
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions',

    'snap_js_url' => $isProduction
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',
];
