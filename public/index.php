<?php

$app = require __DIR__ . '/../bootstrap/app.php';

$request = new \Light\Http\Request(
    $_GET,
    $_POST,
    array(),
    $_COOKIE,
    $_FILES,
    $_SERVER
);

$response = $app->handle($request);
$response->send();

