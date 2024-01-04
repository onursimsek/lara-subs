<?php

return [
    'default_language' => env('DEFAULT_LANGUAGE', 'tr'),

    'providers' => [
        'opensubtitles' => [
            'host' => 'https://api.opensubtitles.com/api/v1',
            'api_key' => env('OPENSUBTITLE_API_KEY'),
            'app_name' => env('OPENSUBTITLE_API_NAME'),

            'credentials' => [
                'username' => env('OPENSUBTITLE_USERNAME'),
                'password' => env('OPENSUBTITLE_PASSWORD'),
            ],
        ],
    ],
];
