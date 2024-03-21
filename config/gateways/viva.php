<?php

$live = env('VIVA_LIVE');

if (in_array($_SERVER['REMOTE_ADDR'], ['82.102.76.201', '51.138.37.238'])){
    $live =0;
}

return [
    'api_key' => $live ? env('VIVA_API_KEY') : env('SK_VIVA_API_KEY'),
    'merchant_id' => $live ? env('VIVA_MERCHANT_ID') : env('SK_VIVA_MERCHANT_ID'),
    'client_id' => $live ? env('VIVA_CLIENT_ID') : env('SK_VIVA_CLIENT_ID'),
    'client_secret' => $live ? env('VIVA_CLIENT_SECRET') : env('SK_VIVA_CLIENT_SECRET'),
    'source_code' => $live ? env('VIVA_SOURCE_CODE') : env('SK_VIVA_SOURCE_CODE') ,
    'environment' => $live ? 'production' : 'demo'
];
