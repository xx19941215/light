<?php
namespace Light\Http;

use Light\App\Http\HttpApp;
use Light\Routing\RouterManager;

class HttpHandler
{
    protected $app;
    protected $router;

    public function __construct(HttpApp $app)
    {
        $this->app = $app;
        $routerManager = new RouterManager($this->app->getBaseDir(), 'http');
        $this->config = $this->app->getConfig();

        if (!$router = $routerManager->getRouter()) {
            $router = $routerManager->buildRouter($this->config->get('router'));
        }

        if ($this->config->get('debug')) {
            $router = $routerManager->buildRouter($this->config->get('router'));
            $routerManager->compile();
        }


        $this->router = $router;
    }

    public function handle(Request $request)
    {
        $route = $this->router->dispatch($request);
        try {
            $this->currentRoute = $route;
            $request->setRoute($route);
            return $this->callControllerAction($request);
        } catch (\Exception $e) {

        }
    }

    protected function callControllerAction(Request $request)
    {
        $route = $request->getRoute();
        list($controllerClass, $fun) = explode('@', $route->getAction());

        if (!class_exists($controllerClass)) {
            throw new \Exception("class not found: $controllerClass");
        }

        $controller = new $controllerClass($this->app, $request);

        if (!method_exists($controller, $fun)) {
            throw new \Exception("method not found: $controllerClass::$fun");
        }

        if ($res = $controller->bootstrap()) {
            return $res;
        }

        return $controller->$fun();
    }
}