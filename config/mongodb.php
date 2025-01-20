<?php

return [
    'host' => env('MONGODB_HOST', 'mongodb'),
    'port' => env('MONGODB_PORT', 27017),
    'database' => env('MONGODB_DATABASE', 'uala-mgo'),
    'username' => env('MONGODB_USERNAME', 'uala'),
    'password' => env('MONGODB_PASSWORD', 'uala123'),
    'options' => [
        'authSource' => 'admin',
        'retryWrites' => true
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