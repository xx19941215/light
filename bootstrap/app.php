<?php
$basePath = realpath(__DIR__ . '/../');

require $basePath . '/vendor/autoload.php';


try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

$app = new \Light\Foundation\App($basePath);

$app->singleton(\Light\Contract\Debug\ExceptionHandler::class,
    Blog\Startup\Exceptions\Handler::class);

$app->withFacades();

return $app;
