<?php
namespace Light\Config;

use Light\Config\Config;

class ConfigManager
{
    protected $config;
    protected $baseDir;
    protected $type;

    public function __construct($baseDir, $type)
    {
        $this->baseDir = $baseDir;
        $this->type = $type;
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

        foreach ($config->get('app', []) as $name => $app) {
            if (! $dir = $this->baseDir . $app['dir'] ?? false) {
                continue;
            }

            $config->set('router', [
                'dir' => [
                    $name => [$dir . '/setting/router']
                ]
            ]);

            $config->includeDir($dir . '/setting/config');
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
        return $this->bashDir . '/cache/setting-config-' . $this->type . '.php';
    }
}