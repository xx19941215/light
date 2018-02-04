<?php

return [
    'work_num' => env('SWOOLE_WORKER_NUM'),
    'max_request' => env('SWOOLE_REQUEST'),
    'log_file' => __DIR__ . '/../storage/logs/swoole.log',
    'enable_static_handler' => true,
    'document_root' => __DIR__ . '/../public/'
];
