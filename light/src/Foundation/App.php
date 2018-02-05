<?php
namespace Light\Foundation;

use Light\Cache\CacheManager;
use Light\Concerns\RouteRequest;
use Light\Config\Config;
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

    public $basePath;
    protected $router;
    protected $ranServiceBinders = [];
    protected $loadedConfigurations = [];

    public function __construct($basePath = '')
    {
        $this->basePath = $basePath;
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
        $this->router = $this->make('routerManager')->buildRouter($this->make('config'));
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
            return new Config;
        });

        $this->configure('app');
        $this->configure('config');
    }

    protected function registerMetaBindings()
    {
        $this->singleton('meta', function () {
            return new Meta($this->make('db.connection', 'meta'), $this->make('cache.store', 'meta'));
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
        $this->configure('database');

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

        $this->configure('i18n');
    }

    protected function registerRouterBindings()
    {
        $this->singleton('router', function () {
            return $this->router;
        });
    }

    protected function registerRouterManagerBindings()
    {
        $this->singleton('routerManager', function() {
            return new RouterManager($this);
        });
    }

    protected function registerSiteManagerBindings()
    {
        $this->configure('site');

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

    public function configure($name)
    {
        if (isset($this->loadedConfigurations[$name])) {
            return;
        }

        $this->loadedConfigurations[$name] = true;

        $path = $this->getConfigurationPath($name);

        if ($path) {
            $this->make('config')->set($name, require $path);
        }
    }

    public function getConfigurationPath($name = null)
    {
        if (! $name) {
            $appConfigDir = $this->basePath('config').'/';

            if (file_exists($appConfigDir)) {
                return $appConfigDir;
            } elseif (file_exists($path = __DIR__.'/../config/')) {
                return $path;
            }
        } else {
            $appConfigPath = $this->basePath('config').'/'.$name.'.php';

            if (file_exists($appConfigPath)) {
                return $appConfigPath;
            } elseif (file_exists($path = __DIR__.'/../config/'.$name.'.php')) {
                return $path;
            }
        }
    }

    public function basePath($path = null)
    {
        if (isset($this->basePath)) {
            return $this->basePath.($path ? '/'.$path : $path);
        }

        if ($this->runningInConsole()) {
            $this->basePath = getcwd();
        } else {
            $this->basePath = realpath(getcwd().'/../');
        }

        return $this->basePath($path);
    }

    public function runningInConsole()
    {
        return php_sapi_name() == 'cli';
    }

    public $availableBindings = [
        'config' => 'registerConfigBindings',
        'cacheManager' => 'registerCacheManagerBindings',
        'cache.store' => 'registerCacheManagerBindings',
        'databaseManager' => 'registerDatabaseManagerBindings',
        'localeManager' => 'registerLocaleManagerBindings',
        'siteManager' => 'registerSiteManagerBindings',
        'meta' => 'registerMetaBindings',
        'router' => 'registerRouterBindings',
        'urlManager' => 'registerUrlManagerBindings',
        'routerManager' => 'registerRouterManagerBindings',
    ];

}