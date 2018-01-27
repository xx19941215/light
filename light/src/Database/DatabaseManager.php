<?php
namespace Light\Database;

use Light\Config\Config;
use Light\Database\Exception\DatabaseException;

class DatabaseManager
{
    protected $optsSet;
    protected $connections;
    protected $serverId;

    public function __construct(Config $config)
    {
        $this->optsSet = $config->get('database');
        $this->serverId = $config->get('server.id');
    }

    public function connect($name)
    {
        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        if (!$opts = prop($this->optsSet, $name)) {
            throw new DatabaseException("cannot find db: $name");
        }

        $driver = prop($opts, 'driver', 'mysql');
        $host = prop($opts, 'host', '');
        $database = prop($opts, 'database', '');
        $port = prop($opts, 'port', 3306);
        $username = $opts['username'];
        $password = $opts['password'];
        $charset = prop($opts, 'charset', 'utf8mb4');

        $dsn = "$driver:host=$host;port=$port;dbname=$database;charset=$charset";

        if (!$driver || !$host || !$database) {
            throw new DatabaseException("$name error db config: $dsn");
        }

        $pdo = new \PDO(
            $dsn,
            $username,
            $password,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_PERSISTENT => false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+00:00'"
            ]
        );

        $class = "Light\\Database\\Connection\\" . ucfirst($driver);
        $this->connections[$name] = new $class($pdo, $this->serverId);

        return $this->connections[$name];
    }
}