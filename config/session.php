<?php

return [
    'cookie_domain' => env('APP_BASE_HOST'),
    'cookie_path' => '/',
    'cookie_lifetime' => 86400000,
    'gc_maxlifetime' => 86400000,
    'name' => 'light_session',
    'save_handler' => 'file',
    'save_path' => __DIR__ . '/../storage/framework/sessions'
];