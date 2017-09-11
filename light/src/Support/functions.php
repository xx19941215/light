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