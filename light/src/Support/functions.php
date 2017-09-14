<?php
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

