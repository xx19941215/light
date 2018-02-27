<?php
namespace Light\Routing;

use Light\Http\Request;
use Light\Routing\Exceptions\RouteMethodNotAllowedException;
use Light\Routing\Exceptions\RouteNotFoundException;
use Light\Tool\IncludeTrait;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;

class Router
{
    use IncludeTrait;
    use AddRouteTrait;

    protected $site;
    protected $app;
    protected $access;
    protected $routeCollectorMap;
    protected $routeMap = [];
    protected $dispatchDataMap = [];

    public function site($site)
    {
        $this->site = $site;
        return $this;
    }

    public function load($data)
    {
        $this->routeMap = $data['routeMap'];
        $this->dispatchDataMap = $data['dispatchDataMap'];
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
                throw new RouteNotFoundException('route not found');
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new RouteMethodNotAllowedException('route method not allowed');
                break;
            case Dispatcher::FOUND:
                $route = $this->routeMap[$res[1]['name']][$res[1]['mode']][$res[1]['method']];
                $route->setParams($res[2]);
                return $route;
        }
    }

    protected function getDispatcher($site) : Dispatcher
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

    public function routeInfo($routeName, $params = [], $mode = '', $method = '')
    {
        $modes = ['ui', 'rest'];
        $methods = ['GET', 'POST'];

        if ($mode) {
            $modes = [$mode];
        }

        if ($method) {
            $methods = [$method];
        }

        if ($set = prop($this->routeMap, $routeName)) {
            foreach ($modes as $mode) {
                if ($sons = prop($set, $mode)) {
                    foreach ($methods as $method) {
                        if ($route = prop($sons, $method)) {
                            $pattern = $route->getPattern();
                            $site = $route->getSite();
                            return [
                               'site' => $site,
                               'path' => $this->replaceRouteParameters($pattern, $params)
                            ];
                        }
                    }
                }
            }
        }

        throw new RouteNotFoundException("Route Not Found: $routeName");
    }

    protected function replaceRouteParameters($pattern, array &$params)
    {
        if ($params) {
            $pattern = $this->pregReplaceSub(
                '/\{.*?\}/',
                $params,
                $this->replaceNamedParameters($pattern, $params)
            );
        }
        return str_replace(['[', ']'], '', preg_replace('/\{.*?\?\}/', '', $pattern));
    }

    protected function pregReplaceSub($pattern, &$replacements, $subject)
    {
        return preg_replace_callback($pattern, function () use (&$replacements) {
            return array_shift($replacements);
        }, $subject);
    }

    protected function replaceNamedParameters($pattern, &$params)
    {
        return preg_replace_callback('/\{(.*?)\??\}/', function ($match) use (&$params) {
            return isset($params[$match[1]]) ? $this->arrPull($params, $match[1]) : $match[0];
        }, $pattern);
    }

    protected function arrPull(&$arr, $key, $default = null)
    {
        $val = prop($arr, $key, $default);
        if (isset($arr[$key])) {
            unset($arr[$key]);
        }
        return $val;
    }
}
