<?php
namespace Light\App;

use Light\Cache\CacheManager;
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
    protected $cmg;

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

        $this->dmg = new DatabaseManager($this->config->get('database'), $this->getServerId());
        return $this->dmg;
    }

    public function getCmg() : CacheManager
    {
        if ($this->cmg) {
            return $this->cmg;
        }

        $this->cmg = new CacheManager($this->config->get('database.redis'));
        return $this->cmg;
    }

    public function getServerId()
    {
        return $this->config->get('server.id');
    }
}