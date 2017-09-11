<?php
namespace Light\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRquest;

class Request extends SymfonyRquest
{
    protected $route;

    public function setRoute(Route $route)
    {
        $this->route = $route;
        return $this->route;
    }

    public function getRoute()
    {
        return $this->route;
    }
}