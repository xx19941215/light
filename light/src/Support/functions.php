<?php
use Light\Support\Str;
use Light\Database\DateSet;

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

function app($make = null)
{
    if (is_null($make)) {
        return \Light\Container\Container::getInstance();
    }

    return \Light\Container\Container::getInstance()->make($make);
}

function config($key = null, $default = null)
{
    if (is_null($key)) {
        return app('config');
    }

    if (is_array($key)) {
        return app('config')->set($key);
    }

    return app('config')->get($key, $default);
}

function storage_path($path = '')
{
    return app()->storagePath($path);
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

function toSfRequest($swRequest) {
    $query = $swRequest->get ?? [];
    $request = $swRequest->post ?? [];
    $cookie = $swRequest->cookie ?? [];
    $files = $swRequest->files ?? [];
    $content = $swRequest->rawContent() ?: null;

    $server = array_change_key_case($swRequest->server, CASE_UPPER);
    foreach ($swRequest->header as $key => $val) {
        $server[sprintf('HTTP_%s', strtoupper(str_replace('-', '_', $key)))] = $val;
    }

    return new \Light\Http\Request($query, $request, [], $cookie, $files, $server);
}

function collect(\Light\Database\SqlBuilder\Mysql\SelectSqlBuilder $ssb, $modelClass)
{
    return new DateSet($ssb, $modelClass);
}