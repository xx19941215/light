<?php
$baseDir = realpath(__DIR__ . '/../');

require $baseDir . '/vendor/autoload.php';

$cmg = new \Light\Config\ConfigManager($baseDir, 'http');

$config = $cmg->buildConfig();

if ($config->get('debug')) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

$app = new \Light\Foundation\App($config);

$app->bind('router', function () use ($config, $baseDir) {
    $rmg = new \Light\Routing\RouterManager($baseDir, 'http');
    return $rmg->buildRouter($config->get('router'));
});