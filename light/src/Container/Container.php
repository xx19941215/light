<?php
namespace Light\Container;

class Container
{
    public $bindings = [];
    public static $instance;
    protected $instances = [];
    protected $aliases = [];
//    public function bind($abstract, $concrete = null)
//    {
//        if (is_null($concrete)) {
//            $concrete = $abstract;
//        }
//
//        if ($concrete instanceof \Closure) {
//            $this->bindings[$abstract] = $concrete;
//        } else {
//            $this->instances[$abstract] = $concrete;
//        }
//    }

    public function bind($abstract, $concrete = null, $shared = false)
    {
       if (! $concrete instanceof \Closure) {
           $concrete = $this->getClosure($abstract, $concrete);
       }

       $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    public function getClosure($abstract, $concrete)
    {
        return function ($container, $parameters = []) use ($abstract, $concrete) {
            $method = ($abstract == $concrete) ? 'build' : 'make';
            return $container->$method($concrete, $parameters);
        };
    }

    public function make($abstract, $parameters = [])
    {

        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->getConcrete($abstract);
        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete, $parameters);
        } else {
            $object = $this->make($concrete, $parameters);
        }

        if ($this->isShared($abstract)) {
            $this->instances[$abstract] = $object;
        }

        return $object;
//        if (isset($this->instances[$abstract])) {
//            return $this->instances[$abstract];
//        }
//
//        array_unshift($parameters, $this);
//        return call_user_func($this->bindings[$abstract], $parameters);
    }

    protected function getConcrete($abstract)
    {
       if (!isset($this->bindings[$abstract])) {
           return $abstract;
       }

       return $this->bindings[$abstract]['concrete'];
    }

    public function build($concrete, $parameters = [])
    {

        if ($concrete instanceof \Closure) {
            return $concrete($this, $parameters);
        }

        $reflector = new \ReflectionClass($concrete);
        if (! $reflector->isInstantiable()) {
            throw new \Exception("Target [$concrete] is not instantiable");
        }

        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $concrete;
        }
        $dependencies = $constructor->getParameters();

        $instances = $this->getDependencies($dependencies, $parameters);

        return $reflector->newInstanceArgs($instances);
    }

    protected function getDependencies($parameters, array $primitives = [])
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
           $dependency = $parameter->getClass();
           if (array_key_exists($parameter->name, $primitives)) {
               $dependencies[] = $primitives[$parameter->name];
           } else if (is_null($dependency)) {
               $dependencies[] = null;
           } else {
               $dependencies[] = $this->resolveClass($parameter);
           }
        }

        return (array) $dependencies;
    }

    protected function resolveClass(\ReflectionParameter $parameter)
    {
        return $this->make($parameter->getClass()->name);
    }


    protected function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof \Closure;
    }

    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    public static function setInstance($container)
    {
        return static::$instance = $container;
    }

    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;

        return $instance;
    }

    public function isShared($abstract)
    {
        return isset($this->instances[$abstract]) ||
            (isset($this->bindings[$abstract]['shared']) &&
                $this->bindings[$abstract]['shared'] === true);
    }

    public function getAlias($abstract)
    {
        if (! isset($this->aliases[$abstract])) {
            return $abstract;
        }

        if ($this->aliases[$abstract] === $abstract) {
            //todo
            throw new \Exception("[{$abstract}] is aliased to itself.");
        }

        return $this->getAlias($this->aliases[$abstract]);
    }


}