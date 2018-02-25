<?php
namespace Light\View;

use Light\Http\UrlManager;

class RegisterUrl extends RegisterBase
{
    public function register(UrlManager $urlManager)
    {
        $this->engine->registerFunction(
            'url',
            function ($site, $uri, $protocol = '') use ($urlManager) {
                return $urlManager->url($site, $uri, $protocol);
            }
        );

        $this->engine->registerFunction(
            'staticUrl',
            function ($uri, $protocol = '') use ($urlManager) {
                return $urlManager->staticUrl($uri, $protocol);
            }
        );

        $this->engine->registerFunction(
            'routeGet',
            function ($name, $params = [], $query = [], $protocol = '') use ($urlManager) {
                return $urlManager->routeGet($name, $params, $query, $protocol);
            }
        );
    }
}
