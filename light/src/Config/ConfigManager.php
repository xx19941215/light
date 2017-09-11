<?php
namespace Light\Config;

class ConfigManager
{
    protected $config;
    protected $baseDir;
    protected $type;

    public function __construct($baseDir, $type)
    {
            
    }

    public function getConfig()
    {
        if ($this->config) {
            return $this->config;
        }
    }

    public function buildConfig()
    {
        
    }

    public function compile()
    {
        
    }

    protected function getCompiledPath()
    {

    }
}