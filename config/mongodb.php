<?php

return [
    'host' => env('MONGO_DB_HOST', 'localhost'),
    'port' => env('MONGO_DB_PORT', 27017),
    'database' => env('MONGO_DB_DATABASE', 'uala-mgo'),
    'username' => env('MONGO_DB_USERNAME', ''),
    'password' => env('MONGO_DB_PASSWORD', ''),
    'options' => [
        'database' => env('MONGO_DB_AUTH_DATABASE', 'admin')
    ],
    'collections' => [
        'tweets' => [
            'indexes' => [
                ['key' => ['user_id' => 1]],
                ['key' => ['created_at' => -1]]
            ]
        ]
    ]
];