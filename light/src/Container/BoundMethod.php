<?php
namespace Light\Container;

class BoundMethod
{
    public static function call($container, $callback, array $parameters = [], $defaultMethod = null)
    {
        if (static::isCallableWithAtSign($callback) || $defaultMethod) {
            return static::callClass($container, $callback, $parameters, $defaultMethod);
        }

        return static::callBoundMethod($container, $callback, function () use ($container, $callback, $parameters) {
            return call_user_func_array(
                $callback, static::getMethodDependencies($container, $callback, $parameters)
            );
        } );
    }

    protected static function callClass($container, $target, array $parameters = [], $defaultMethod = null)
    {
        $segments = explode('@', $target);
        $method = count($segments) == 2 ? $segments[1] : $defaultMethod;

        if (is_null($method)) {
            throw new \InvalidArgumentException('Method not provided');
        }

        return static::call(
            $container, [$container->make($segments[0]), $method], $parameters
        );
    }

    protected static function callBoundMethod($container, $callback, $default)
    {
        return $default instanceof \Closure ? $default() : $default;
    }

    protected static function normalizeMethod($callback)
    {

    }

    protected static function getMethodDependencies($container, $callback, array $parameters = [])
    {
       $dependencies = [];
       foreach (static::getCallReflector($callback)->getParameters() as $parameter) {
           static::addDependencyForCallParameter($container, $parameter, $parameters, $dependencies);
       }

       return array_merge($dependencies, $parameters);
    }

    protected static function getCallReflector($callback)
    {
        if (is_string($callback) && strpos($callback, '::') !== false) {
            $callback = explode('::', $callback);
        }

        return is_array($callback)
            ? new \ReflectionMethod($callback[0], $callback[1])
            : new \ReflectionFunction($callback);
    }

    protected static function addDependencyForCallParameter($container, $parameter, array &$parameters, &$dependencies)
    {
        if (array_key_exists($parameter->name, $parameters)) {
            $dependencies[] = $parameters[$parameter->name];
            unset($parameters[$parameter->name]);
        } elseif ($parameter->getClass()) {
            $dependencies[] = $container->make($parameter->getClass()->name);
        } elseif ($parameter->isDefaultValueAvailable()) {
            $dependencies[] = $parameter->getDefaultValue();
        }
    }

    protected static function isCallableWithAtSign($callback)
    {
       return is_string($callback) && strpos($callback, '@') !== false;
    }
}