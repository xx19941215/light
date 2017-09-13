<?php
namespace Light\Routing;

use Light\Http\Request;
use Light\Tool\IncludeTrait;
use Light\Routing\RouteCollector;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;

class Router
{
    use IncludeTrait;

    protected $site;
    protected $app;
    protected $access;
    
    protected $routeMap = [];
    protected $dispatchDataMap = [];

    public function site($site)
    {
        $this->siet = $site;
        return $this;
    }

    public function app($app)
    {
        $this->app = $app;
        return $this;
    }

    public function access($access)
    {
        $this->access = $access;
        return $this;
    }

    public function getRouteMap()
    {
        return $this->routeMap;
    }

    public function getDispatchDataMap()
    {
        if ($this->dispatchDataMap) {
            return $this->dispatchDataMap;
        }
    }
    
    public function dispatch(Request $request)
    {
        $site = $request->getSite();
        $dispatcher = $this->getDispatcher($site);
        
        $res = $dispatcher->dispatch(
            $request->getMethod(),
            $request->getPath()
        );

        switch ($res[0]) {
            case Dispatcher::NOT_FOUND:
                throw new \Exception('route not found');
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new \Exception('route method not allowed');
                break;
            case Dispatcher::FOUND:
                $route = $this->routeMap[$res[1]['name']][$res[1]['mode']][$res[1]['method']];
                $route->setParams($res[2]);
                return $route;
        }
    }

    protected function getDispatcher($site)
    {
        if (!$dispatchData = prop($this->dispatchDataMap, $site, [])) {
            $dispatchData = $this->getRouteCollector($site)->getData();
        }

        return new Dispatcher($dispatchData);
    }

    protected function getRouteCollector($site)
    {
        if (isset($this->routeCollectorMap[$site])) {
            return $this->routeCollectorMap[$site];
        }

        $routeCollector = new RouteCollector($site);
        $this->routeCollectorMap[$site] = $routeCollector;

        return $this->routeCollectorMap[$site];
    }

}