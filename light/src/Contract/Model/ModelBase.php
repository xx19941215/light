<?php
namespace Light\Contract\Model;

abstract class ModelBase implements \JsonSerializable
{
    public function __construct($data = [])
    {
        $this->init();
        $this->load($data);
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function getData(): array
    {
        return get_object_vars($this);
    }

    public function init ()
    {
    }

    public function __set($key, $value)
    {
        if ($flagPos = strpos($key, '_')) {
            $subKey = substr($key, $flagPos + 1);

            $modelName = substr($key, 0, $flagPos);
            if ($this->$modelName) {
                $this->$modelName->$subKey = $value;
                return;
            }

            $getModelFun = 'get' . $modelName;
            if ($model = $this->$getModelFun()) {
                $model->$subKey = $value;
                return;
            }

            throw new \OutOfBoundsException("Cannot find " . $key . ' in' . static::class);
        }
    }

    public function load($data = [])
    {
        foreach ($data as $key => $val) {
            if (is_array($val) &&
                property_exists($this, $key) &&
                $this->$key instanceof self
            ) {
                $this->$key->load($val);
                continue;
            }

            $this->$key = $val;
        }
    }

    public function get($key)
    {
        return $this->callGet('get' . ucfirst($key));
    }

    public function __call($method, $args)
    {
        $prefix = substr($method, 0, 3);

        if ($prefix == 'get') {
            return $this->callGet($method);
        }

        if ($prefix == 'set') {
            return $this->callSet($method, $args);
        }
    }

    protected function extractVar($method)
    {
        $var = lcfirst(substr($method, 3));
        if (property_exists($this, $var)) {
            return $var;
        }

        throw new \Exception("$var-not-exists in" . get_class($this));
    }

    protected function setVar($key, $val)
    {
       if (property_exists($this, $key)) {
           $this->$key = $val;
       }

       throw new \Exception("$key-not-exists-in" . get_class($this));
    }

    protected function callGet($method)
    {
       if ($var = $this->extractVar($method)) {
           return $this->$var;
       }

       return '';
    }

    protected function callSet($method, $args)
    {
        if (!isset($args[0])) {
            return;
        }

        if ($var = $this->extractVar($method)) {
            $this->$var = $args[0];
            return;
        }
    }
}