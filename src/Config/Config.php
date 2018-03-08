<?php
namespace Light\Config;

use Light\Support\Arr;

class Config implements \ArrayAccess
{
    protected $items = [];

    public function __construct(array $items = [])
    {
        if ($items) {
            $this->load($items);
        }
    }

    public function load($items)
    {
        $this->items = array_merge_recursive($this->items, $items);
    }
    

    public function get($key, $default = null)
    {
        return Arr::get($this->items, $key, $default);
    }

    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $subKey => $subValue) {
                $this->set($subKey, $subValue);
            }

            return $this;
        }

        if (is_string($key)) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $this->set($key . '.' . $subKey, $subValue);
                }

                return $this;
            }

            Arr::set($this->items, $key, $value);
            return $this;
        }

        throw new \RuntimeException('config::set error format');
    }

    public function all()
    {
        return $this->items;
    }

    public function has($key)
    {
        return Arr::has($this->items, $key);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }
}
