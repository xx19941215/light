<?php
namespace Light\Contract\Controller;

use Light\Foundation\App;
use Light\Config\Config;
use Light\Http\Request;
use Light\Routing\Router;
use Symfony\Component\HttpFoundation\Response;

trait ControllerTrait
{
    protected $app;
    protected $request;
    protected $config;
    protected $urlManager;
    protected $params = [];

    public function __construct($app, Request $request)
    {
        $this->app = $app;
        $this->config = $app->make('config');
        $this->request = $request;

        if ($route = $request->getRoute()) {
            $this->params = $route->getParams();
        }
    }

    public function bootstrap()
    {
    }

    protected function response($content) : Response
    {
        return new Response($content);
    }

    protected function getRequest() : Request
    {
        return $this->request;
    }

    public function getApp() : App
    {
        return $this->app;
    }

    public function getConfig() : Config
    {
        return $this->config;
    }

    public function getRouter() : Router
    {
        return $this->app->make('router');
    }

    public function getUrlManager()
    {
        if ($this->urlManager) {
            return $this->urlManager;
        }

        $this->urlManager = $this->app->make('urlManager');
        return $this->urlManager;
    }

    protected function getParam($key, $default = null)
    {
        return prop($this->params, $key, $default);
    }
}