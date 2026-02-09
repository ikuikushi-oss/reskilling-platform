<?php

return [
    'mode' => env('ZOOM_MODE', 'mock'),
    'account_id' => env('ZOOM_ACCOUNT_ID'),
    'client_id' => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),
    'host_user_id' => env('ZOOM_HOST_USER_ID', 'me'),
];
