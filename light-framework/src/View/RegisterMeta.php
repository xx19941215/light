<?php
namespace Light\View;

use Light\Meta\Meta;

class RegisterMeta extends RegisterBase
{
    public function register(Meta $meta)
    {
        $this->engine->registerFunction(
            'meta',
            function ($str, $vars = [], $localeKey = '') use ($meta) {
                return $meta->get($str, $vars, $localeKey);
            }
        );
    }
}
