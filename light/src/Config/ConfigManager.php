<?php
namespace Light\Config;

use Light\Foundation\App;

class ConfigManager
{
    protected $config;
    protected $baseDir;

    public function __construct(App $app)
    {
        $this->baseDir = $app->baseDir;
    }

    public function getConfig()
    {
        if ($this->config) {
            return $this->config;
        }

        $compilePath = $this->getCompiledPath();
        if (file_exists($compilePath)) {
            $config = new Config();
            $config->load(require $compilePath);
            $this->config = $config;

            return $this->config;
        }

        return null;
    }

    public function buildConfig()
    {
        $config = new Config();
        $config->set('baseDir', $this->baseDir);

        $config->includeFile($this->baseDir . '/config/app.php');
        $config->includeFile($this->baseDir . '/config/config.php');
        $config->includeFile($this->baseDir . '/config/site.php');
        $config->includeFile($this->baseDir . '/config/database.php');

        foreach ($config->get('app', []) as $name => $app) {
            if (! $dir = $this->baseDir . $app['dir'] ?? false) {
                continue;
            }

            $config->set('router', [
                'dir' => [
                    $name => [$dir . '/setting/router']
                ]
            ]);
        }

        $this->config = $config;
        return $this->config;
    }

    public function compile()
    {
        $compilePath = $this->getCompiledPath();
        var2file($compilePath, $this->config->all());
    }

    protected function getCompiledPath()
    {
        return $this->baseDir . '/cache/setting-config.php';
    }
}