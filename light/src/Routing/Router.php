<?php
namespace Light\Routing;

use Light\Tool\IncludeTrait;

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
}