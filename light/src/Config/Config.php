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
        $key = is_array($key) ? $key : [$key => $value];
        
        foreach ($keys as $key => $value) {
            Arr::set($this->items, $key, $value);
        }
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
        return $this->set($key, $value);
    }

    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }
}
