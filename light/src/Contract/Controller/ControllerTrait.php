<?php
namespace Light\Contract\Controller;

use Light\App\App;
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

    public function __construct(App $app, Request $request)
    {
        $this->app = $app;
        $this->config = $app->getConfig();
        $this->request = $request;
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
        return $this->app->getRouter();
    }

    public function getUrlManager()
    {
        if ($this->urlManager) {
            return $this->urlManager;
        }

        $this->urlManager = $this->app->getUrlManager($this->request);
        return $this->urlManager;
    }
}