<?php
$baseDir = $this->get('baseDir');

$this
    ->set('debug', false)
    ->set('baseHost', 'light.xiao')
    ->set('local', [
        'db' => [
            'host' => 'db',
            'username' => 'root',
            'password' => '123456789'
        ],
        'cache' => [
            'host' => 'redis'
        ],
        'session' => [
            'save_path' => 'tcp://redis:6379?database=10'
        ]
    ]);

