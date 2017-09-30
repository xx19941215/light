<?php
namespace Light\Foundation;

use Light\Config\Config;
use Light\Container\Container;

class App extends Container
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }
}