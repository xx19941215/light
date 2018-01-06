<?php
namespace Light\Http;

use Light\Routing\Router;

class UrlManager
{
    protected $router;
    protected $request;

    public function __construct(Router $router, Request $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    public function url($site, $uri, $protocol = '')
    {
       if (!$protocol) {
           $protocol = $this->request->isSecure() ? 'https://' : 'http://';
       }

       $host = $this->request->getSiteManager()->getHost($site);

       if ($uri[0] !== '/') {
           $uri = '/' . $uri;
       }

       return $protocol . $host . $uri;
    }

    public function staticUrl($uri, $protocol = '')
    {
        return $this->url('static', $uri, $protocol);
    }

    public function routeUrl($name, $params = [], $query = [], $protocol = '', $mode = '', $method = '')
    {
        if (!$protocol) {
            $protocol = $this->request->isSecure() ? 'https://' : 'http://';
        }

        $routeInfo = $this->router->routeInfo($name, $params, $mode, $method);
        $host = $this->request->getSiteManager()->getHost($routeInfo['site']);

        if ('path' === $this->request->getLocaleMode()) {
            $localeKey = $this->request->getLocaleKey();
            return $protocol . $host . '/' . $localeKey . $routeInfo['path']
                . ($query ? ('?' . http_build_query($query)) : '');
        }

        return $protocol . $host . $routeInfo['path']
            . ($query ? ('?' . http_build_query($query)) : '');
    }

    public function routeGet($name, $params = [], $query = [], $protocol = '')
    {
        return $this->routeUrl($name, $params, $query, $protocol, 'ui', 'GET');
    }
}