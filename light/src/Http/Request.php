<?php
namespace Light\Http;

use Light\Routing\Route;
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

    public function getSite()
    {
        $host = $this->getHost();
    }
}