<?php
$basePath = realpath(__DIR__ . '/../');

require $basePath . '/vendor/autoload.php';



try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

if (env('APP_DEBUG')) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

$app = new \Light\Foundation\App($basePath);


return $app;
