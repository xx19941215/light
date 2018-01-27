<?php
namespace Light\Concerns;

use Light\Http\Request;

trait RouteRequest
{
    protected $currentRoute;
    protected $dispatcher;

    public function handle(Request $request)
    {
        $this->instance(Request::class, $request);
        $request->setSiteManager($this->make('siteManager'));
        $request->setLocaleManager($this->make('localeManager'));

        $route = $this->router->dispatch($request);

        try {
            $this->currentRoute = $route;
            $request->setRoute($route);
            return $this->callControllerAction($request);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function callControllerAction(Request $request)
    {
        $route = $request->getRoute();
        list($controllerClass, $fun) = explode('@', $route->getAction());

        if (!class_exists($controllerClass)) {
            throw new \Exception("class not found: $controllerClass");
        }

        $controller = new $controllerClass($this->make('app'), $request);

        if (!method_exists($controller, $fun)) {
            throw new \Exception("method not found: $controllerClass::$fun");
        }

        if ($res = $controller->bootstrap()) {
            return $res;
        }

        return $controller->$fun();
    }
}