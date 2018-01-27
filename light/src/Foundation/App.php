<?php
namespace Light\Foundation;

use Light\Cache\CacheManager;
use Light\Concerns\RouteRequest;
use Light\Config\ConfigManager;
use Light\Container\Container;
use Light\Database\DatabaseManager;
use Light\Http\SiteManager;
use Light\Http\UrlManager;
use Light\I18n\Locale\LocaleManager;
use Light\Meta\Meta;
use Light\Routing\RouterManager;

class App extends Container
{
    use RouteRequest;

    public $baseDir;
    protected $router;
    protected $ranServiceBinders = [];

    public function __construct($baseDir = '')
    {
        $this->baseDir = $baseDir;
        $this->bootstrapContainer();
        $this->bootstrapRouter();
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
            'Light\Config\DatabaseManager' => 'databaseManager',
            'Light\I18n\Locale\LocaleManager' => 'localeManager',
            'Light\Http\SiteManager' => 'siteManager',
            'Light\Meta\Meta' => 'meta',
            'Light\Routing\Router' => 'router',
            'request' => 'Light\Http\Request',
        ];
    }

    protected function registerConfigBindings()
    {
        $this->singleton('config', function () {
            return $this->make('configManager')->buildConfig();
        });
    }

    protected function registerMetaBindings()
    {
        $this->singleton('meta', function () {
            return new Meta($this->make('db.connection', 'meta'), $this->make('cache.store', 'meta'));
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

        $this->singleton('cache.store', function ($app, $name) {
            return $app->make('cacheManager')->connect($name);
        });
    }

    protected function registerDatabaseManagerBindings()
    {
        $this->singleton('databaseManager', function () {
            return new DatabaseManager($this->make('config'));
        });

        $this->singleton('db.connection', function ($app, $name) {
            return $app->make('databaseManager')->connect($name);
        });
    }

    protected function registerLocaleManagerBindings()
    {
        $this->singleton('localeManager', function () {
            return new LocaleManager($this->make('config'));
        });
    }

    protected function registerRouterBindings()
    {
        $this->singleton('router', function () {
            return $this->router;
        });
    }

    protected function registerSiteManagerBindings()
    {
        $this->singleton('siteManager', function () {
            return new SiteManager($this->make('config'));
        });
    }

    protected function registerUrlManagerBindings()
    {
        $this->singleton('urlManager', function() {
            return new UrlManager($this->router, $this->make('request'));
        });
    }

    public $availableBindings = [
        'config' => 'registerConfigBindings',
        'configManager' => 'registerConfigManagerBindings',
        'cacheManager' => 'registerCacheManagerBindings',
        'cache.store' => 'registerCacheManagerBindings',
        'databaseManager' => 'registerDatabaseManagerBindings',
        'localeManager' => 'registerLocaleManagerBindings',
        'siteManager' => 'registerSiteManagerBindings',
        'meta' => 'registerMetaBindings',
        'router' => 'registerRouterBindings',
        'urlManager' => 'registerUrlManagerBindings',
    ];

}