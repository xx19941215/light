<?php
namespace Light\App;

use Light\Config\Config;
use Light\Config\ConfigManager;
use Light\Database\DatabaseManager;

class App
{
    protected $type = 'http';
    protected $isDebug = false;
    protected $baseDir;
    protected $config;
    
    protected $dmg;
    
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

    public function getConfig() : Config
    {
        return $this->config;
    }

    public function getDmg() : DatabaseManager
    {
        if ($this->dmg) {
            return $this->dmg;
        }
        $this->dmg = new DatabaseManager($this->config->get('local.db'), $this->getServerId());
        return $this->dmg;
    }

    public function getServerId()
    {
        return $this->config->get('server.id');
    }
}