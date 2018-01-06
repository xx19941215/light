<?php
namespace Light\Routing;

class Route
{
    protected $status;
    protected $name;
    protected $action;
    protected $site;
    protected $app;
    protected $mode;
    protected $access;
    protected $params;
    protected $pattern;
    protected $method;

    public function __set_state($data)
    {
        return new Route($data);
    }

    public function __construct($data)
    {
        if (! isset($data['name'])) {
            throw new \Exception('route name cannot be empty');
        }

        if (! isset($data['site'])) {
            throw new \Exception('route site cannot be empty');
        }

        $this->method = $data['method'];
        $this->status = $data['status'] ?? 0;
        $this->name = $data['name'];
        $this->action = $data['action'];
        $this->site = $data['site'];
        $this->app = $data['app'];
        $this->mode = $data['mode'] ?? 'ui';
        $this->access = $data['access'];
        $this->params = $data['params'] ?? [];
        $this->pattern = $data['pattern'];
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getApp()
    {
        return $this->app;
    }

    public function getParams()
    {
        return $this->params;
    }
}