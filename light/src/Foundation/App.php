<?php
namespace Light\Foundation;

use Light\Cache\CacheManager;
use Light\Config\ConfigManager;
use Light\Container\Container;
use Light\Routing\RouterManager;

class App extends Container
{
    public $baseDir;
    protected $router;
    protected $ranServiceBinders = [];

    public function __construct($baseDir = '')
    {
        $this->baseDir = $baseDir;
        $this->bootstrapContainer();
    }

    protected function bootstrapContainer()
    {
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance('Light\Foundation\App', $this);
        $this->registerContainerAliases();
    }

    public function bootstrapRouter()
    {
        $routerManager = new RouterManager($this);
        $this->router = $routerManager->buildRouter($this->make('config')->get('router'));
    }

    public function make($abstract, $parameters = [])
    {
        $abstract = $this->getAlias($abstract);

        if (array_key_exists($abstract, $this->availableBindings) &&
        ! array_key_exists($this->availableBindings[$abstract], $this->ranServiceBinders)) {
            $this->{$method = $this->availableBindings[$abstract]}();

            $this->ranServiceBinders[$method] = true;
        }

        return parent::make($abstract, $parameters);
    }

    protected function registerContainerAliases()
    {
        $this->aliases = [
            'Light\Config\Config' => 'config',
            'Light\Foundation\App' => 'app',
            'Light\Config\ConfigManager' => 'cacheManager',
        ];
    }

    protected function registerConfigBindings()
    {
        $this->singleton('config', function () {
            return $this->make('configManager')->buildConfig();
        });
    }

    protected function registerConfigManagerBindings()
    {
        $this->singleton('configManager', function () {
            return new ConfigManager($this);
        });
    }

    protected function registerCacheManagerBindings()
    {
        $this->singleton('cacheManager', function () {
            return new CacheManager($this);
        });
    }


    public $availableBindings = [
        'config' => 'registerConfigBindings',
        'configManager' => 'registerConfigManagerBindings',
        'cacheManager' => 'registerCacheManagerBindings',
    ];

}