<?php
namespace Light\Foundation;

use Light\Container\Container;
use Light\Routing\RouterManager;

class App extends Container
{
    public $baseDir;
    protected $router;

    public function __construct($baseDir = '')
    {
        $this->baseDir = $baseDir;
        $this->bootstrapContainer();
    }

    protected function bootstrapContainer()
    {
        static::setInstance($this);
        $this->instance('Light\Foundation\App', $this);
        $this->registerContainerAliases();
    }

    public function bootstrapRouter()
    {
        $routerManager = new RouterManager($this->baseDir);
        $this->router = $routerManager->buildRouter($this->make('config')->get('router'));
    }

    public function make($abstract, $parameters = [])
    {
        $abstract = $this->getAlias($abstract);
        return parent::make($abstract, $parameters);
    }

    protected function registerContainerAliases()
    {
        $this->aliases = [
            'Light\Config\Config' => 'config',
        ];
    }
}