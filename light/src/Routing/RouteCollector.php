<?php
namespace Light\Routing;

use FastRoute\RouteParser\Std as RouteParser;
use FastRoute\RouteCollector as FastRouteCollector;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;

class RouteCollector
{
    protected $site;
    protected $collector;

    public function __construct($site)
    {
        $this->site = $site;
        $this->collector = new FastRouteCollector(new RouteParser, new DataGenerator);
    }

    public function addRoute(Route $route)
    {
        $this->collector->addRoute(
            $route->getMethod(),
            $route->getPattern(),
            [
                'name' => $route->getName(),
                'mode' => $route->getMode(),
                'method' => $route->getMethod()
            ]
        );
    }

    public function getData()
    {
        return $this->collector->getData();
    }
}