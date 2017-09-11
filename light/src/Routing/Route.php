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
        $this->status = $data['status'];
        $this->name = $data['name'];
        $this->action = $data['action'];
        $this->site = $data['site'];
        $this->app = $data['app'];
        $this->mode = $data['mode'] ?? 'ui';
        $this->access = $data['access'];
        $this->params = $data['params'];
        $this->pattern = $data['pattern'];
    }
}