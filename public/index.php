<?php
$baseDir = realpath(__DIR__ . '/../');

require $baseDir . 'vendor/autoload.php';

$request = new \Light\Http\Request(
    $_GET,
    $_POST,
    array(),
    $_COOKIE,
    $_FILES,
    $_SERVER
);

$response = app($baseDir, 'http')->handle($request);
$response->send();