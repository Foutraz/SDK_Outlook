<?php

return [
    'client_id' => env('OUTLOOK_CLIENT_ID', env('AZURE_CLIENT_ID')),
    'client_secret' => env('OUTLOOK_CLIENT_SECRET', env('AZURE_CLIENT_SECRET')),
    'redirect_uri' => env('OUTLOOK_REDIRECT_URI', env('AZURE_REDIRECT_URI')),
    'tenant' => env('OUTLOOK_TENANT', env('AZURE_TENANT', 'common')),
    'endpoint' => env('OUTLOOK_BASE_URL', 'https://graph.microsoft.com/v1.0'),
    'token' => env('OUTLOOK_TOKEN'),
];
