<?php
namespace Light\Routing;

use Light\Foundation\App;

class RouterManager
{
    protected $router;
    protected $baseDir;

    public function __construct(App $app)
    {
        $this->baseDir = $app->baseDir;
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

    public function buildRouter($opts)
    {
        $router = new Router();
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
        return $this->baseDir . '/cache/setting-router.php';
    }
}