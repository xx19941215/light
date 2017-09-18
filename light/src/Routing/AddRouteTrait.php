<?php
namespace Light\Routing;

trait AddRouteTrait
{
    public function addRoute(array $opts = [])
    {
        $opts['site'] = $this->site;
        $opts['app'] = $this->app;
        $opts['access'] = $this->access;

        $route = new Route($opts);
        $routeCollector = $this->getRouteCollector($route->getSite());
        $routeCollector->addRoute($route);

        $name = $route->getName();
        $mode = $route->getMode();
        $httpMethod = $route->getMethod();

        if (isset($this->routeMap[$name][$mode][$httpMethod])) {
            throw new \Exception("route $name - $mode - $httpMethod already exists");
        }

        $this->routeMap[$name][$mode][$httpMethod] = $route;
    }

    public function get($pattern, $name, $action)
    {
        $this->addRoute([
            'method' => 'GET',
            'pattern'=> $pattern,
            'name' => $name,
            'action' => $action
        ]);

        return $this;
    }
}