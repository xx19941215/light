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
}