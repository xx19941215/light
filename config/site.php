<?php

return [
    'www' => [
        'host' => 'www.' . env('APP_BASE_HOST'),
    ],
    'static' => [
        'host' => 'static.' . env('APP_BASE_HOST'),
        'dir' => __DIR__ . '/../public',
    ],
];