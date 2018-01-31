<?php

return [
    'redis' => [
        'default' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'database' => 0
        ],
        'meta' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'database' => 1
        ],
    ],
    'meta' => [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'driver' => 'mysql',
        'database' => env('DB_DATABASE'),
        'host' => env('DB_HOST'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD')
    ],
    'default' => [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'driver' => 'mysql',
        'database' => env('DB_DATABASE'),
        'host' => env('DB_HOST'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD')
    ]
];
