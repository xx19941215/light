<?php
namespace Light\Contract\Service;

use Light\Foundation\App;
use Light\Config\Config;
use Light\Database\DatabaseManager;

abstract class ServiceBase
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    protected function getConfig() : Config
    {
        return $this->app->getConfig();
    }

    protected function getDmg() : DatabaseManager
    {
        return $this->app->make('databaseManager');
    }
}
