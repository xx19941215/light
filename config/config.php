<?php
return [
    'debug' => env('APP_DEBUG'),
    'baseHost' => env('APP_BASE_HOST'),
    'server' => [
        'id' => 1
    ],
    'i18n' => env('I18N') ?: false,
    'isSwoole' => env('SWOOLE') ?: false
];