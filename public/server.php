<?php
$app = require __DIR__ . '/../bootstrap/app.php';

$app->configure('swoole');

$swooleConfig = $app->make('config')->get('swoole');

$http = new swoole_http_server('127.0.0.1', '8888');

$http->set($swooleConfig);

$http->on('request', function ($swRequest, $swResponse) use ($app) {
    // 每次请求都只会执行这里面的代码，不用再初始化框架内核，运行性能大大提高！
//    $static = __DIR__.$swRequest->server['path_info'];
//    if (file_exists($static)) {
//        $ext = pathinfo($static, PATHINFO_EXTENSION);
//        $swResponse->header('Content-Type', sprintf('text/%s', $ext));
//        $swResponse->end(file_get_contents($static));
//
//        return;
//    }

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
