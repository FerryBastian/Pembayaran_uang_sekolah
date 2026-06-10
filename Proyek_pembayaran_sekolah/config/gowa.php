<?php

return [
    'api_url' => env('GOWA_API_URL', 'http://127.0.0.1:3000'),
    'username' => env('GOWA_USERNAME'),
    'password' => env('GOWA_PASSWORD'),
    'device_id' => env('GOWA_DEVICE_ID'),
    'timeout' => (int) env('GOWA_TIMEOUT', 15),
];
