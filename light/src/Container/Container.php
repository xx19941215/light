<?php
namespace Light\Container;

class Container
{
    protected static $instance;
    protected $bindings = [];
    protected $instances = [];

    public function bind($abstract, $concrete = null)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if ($concrete instanceof \Closure) {
            $this->bindings[$abstract] = $concrete;
        } else {
            $this->instances[$abstract] = $concrete;
        }
    }

    public function make($abstract, $parameters = [])
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        array_unshift($parameters, $this);
        return call_user_func($this->bindings[$abstract], $parameters);
    }

    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete);
    }
}