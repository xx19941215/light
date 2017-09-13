<?php
namespace Light\Http;

use Light\App\Http\HttpApp;

class HttpHandler
{
    protected $app;
    protected $router;

    public function __construct(HttpApp $app)
    {
        $this->app = $app;
        
    }
}