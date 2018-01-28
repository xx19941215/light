<?php
use Light\Support\Str;

function var2file($targetPath, $var)
{
    file_put_contents(
        $targetPath,
        '<?php return ' . var_export($var, true) . ';'
        );
}

function value($value)
{
    return $value instanceof Closure ? $value() : $value;
}

function obj($obj)
{
    return $obj;
}

function app($baseDir = '', $type = 'http')
{
    static $app;
    if ($app) {
        return $app;
    }

    if ($type == 'http') {
        $app =  new \Light\App\Http\HttpApp($baseDir);
        return $app;
    }
}

function prop($arr, $key, $default = '')
{
    return isset($arr[$key]) ? $arr[$key] : $default;
}

function dd($debug)
{
    var_dump($debug);
    exit;
}

function env($key, $default = null)
{
    $value = getenv($key);

    if ($value === false) {
        return value($default);
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return;
    }

    if (strlen($value) > 1 && Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
        return substr($value, 1, -1);
    }

    return $value;
}
