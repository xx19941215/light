<?php
namespace Light\Routing;

use Light\Config\Config;
use Light\Foundation\App;

class RouterManager
{
    protected $router;
    protected $basePath;

    public function __construct(App $app)
    {
        $this->basePath = $app->basePath;
    }

    public function getRouter()
    {
        if ($this->router) {
            return $this->router;
        }

        $compiledPath = $this->getCompiledPath();
        if (file_exists($compiledPath)) {
            $router = new Router();
            $router->load(require $compiledPath);
            $this->router = $router;
            return $this->router;
        }

        return null;
    }

    public function buildRouter(Config $config)
    {
        $router = new Router();

        foreach ($config->get('app', []) as $name => $app) {
            if (! $dir = $this->basePath . $app['dir'] ?? false) {
                continue;
            }

            $config->set('router', [
                'dir' => [
                    $name => [$dir . '/setting/router']
                ]
            ]);
        }

        $opts = $config->get('router');

        foreach (prop($opts, 'dir', []) as $app => $dirs) {
            foreach ($dirs as $dir) {
                $router->app($app);
                $router->includeDir($dir);
            }
        }

        foreach (prop($opts, 'file', []) as $files) {
            foreach ($files as $file) {
                $router->includeFile($file);
            }
        }


        $this->router = $router;
        return $this->router;
    }

    public function compile()
    {
        var2file(
            $this->getCompiledPath(),
            [
                'routeMap' => $this->router->getrouteMap(),
                'dispatchDataMap' => $this->router->getDispatchDataMap()
            ]
        );
    }

    protected function getCompiledPath()
    {
        return $this->basePath . '/cache/setting-router.php';
    }
}