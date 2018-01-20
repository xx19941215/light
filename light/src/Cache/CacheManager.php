<?php
namespace Light\Cache;

use Light\Config\Config;

class CacheManager
{
    protected $cnns;
    protected $opts;

    public function __construct(Config $config)
    {
        $this->opts = $config->get('database.redis');
    }

    public function connect($name)
    {
       if (isset($this->cnns[$name])) {
          return $this->cnns[$name];
       }

       if (!$opts = prop($this->opts, $name)) {
           throw new \Exception("cannot find config for cache [$name]");
       }

       if ('redis' === prop($opts, 'adapter', 'redis')) {
           $host = prop($opts, 'host', '127.0.0,1');
           $port = prop($opts, 'port', 6379);
           $database = prop($opts, 'database', 0);

           $redis = new \Redis();
           $redis->connect($host, $port);
           $redis->select($database);

           $this->conns[$name] = $redis;
       }

       return $this->conns[$name];
    }
}