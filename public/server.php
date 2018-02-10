<?php
$app = require __DIR__ . '/../bootstrap/app.php';

$app->configure('swoole');

$swooleConfig = $app->make('config')->get('swoole');
$app->inSwoole = true;

$http = new swoole_http_server('127.0.0.1', '8888');

$http->set($swooleConfig);

$http->on('request', function ($swRequest, $swResponse) use ($app) {
    $request = toSfRequest($swRequest);
    $response = $app->handle($request);
    foreach ($response->headers->allPreserveCase() as $key => $vals) {
        foreach ($vals as $val) {
            $swResponse->header($key, $val);
        }
    }

    $swResponse->end($response->getContent());

});

$http->start();
