<?php
$baseDir = realpath(__DIR__ . '/../');

require $baseDir . '/vendor/autoload.php';

$app = new \Light\Foundation\App($baseDir);

if ($app->make('config')->get('debug')) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

$app->bootstrapRouter();

return $app;

