<?php
$baseDir = realpath(__DIR__ . '/../');

require $baseDir . '/vendor/autoload.php';

$app = new \Light\Foundation\App($baseDir);

$app->bind('configManager', \Light\Config\ConfigManager::class);

$app->singleton('config', function () use ($app) {
    return $app->make('configManager')->buildConfig();
});

$app->bind('cmg', \Light\Cache\CacheManager::class);
$app->make('cmg');

if ($app->make('config')->get('debug')) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

$app->bootstrapRouter();

