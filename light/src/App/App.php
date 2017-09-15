<?php
namespace Light\App;

use Light\Config\ConfigManager;

class App
{
    protected $type = 'http';
    protected $isDebug = false;
    protected $baseDir;
    protected $config;

    public function __construct($baseDir)
    {
        $configManager = new ConfigManager($baseDir, $this->type);

        if (! $config = $configManager->getConfig()) {
            $config = $configManager->buildConfig();
        }


        $this->isDebug = $config->get('debug');

        if ($this->isDebug == true) {
            $config = $configManager->buildConfig();
            $configManager->compile();
        }

        $this->baseDir = $baseDir;
        $this->config = $config;
    }

    public function getBaseDir()
    {
        return $this->baseDir;
    }

    public function getConfig()
    {
        return $this->config;
    }
}