<?php
$app = require __DIR__ . '/../bootstrap/app.php';

$app->configure('swoole');

$swooleConfig = $app->make('config')->get('swoole');

$http = new swoole_http_server('127.0.0.1', '8888');

$http->set($swooleConfig);

$http->on('request', function ($request, $response) use ($app) {

});

$http->start();
