<?php
$localDbHost = $this->get('local.db.host');
$localDbUsername = $this->get('local.db.username');
$localDbPassword = $this->get('local.db.password');
$localDb = $this->get('local.db.database');

$this->set('database', [
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
        'database' => $localDb,
        'host' => $localDbHost,
        'username' => $localDbUsername,
        'password' => $localDbPassword
    ],
    'default' => [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'driver' => 'mysql',
        'database' => $localDb,
        'host' => $localDbHost,
        'username' => $localDbUsername,
        'password' => $localDbPassword
    ]
]);
