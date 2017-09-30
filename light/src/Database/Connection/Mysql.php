<?php
namespace Light\Database\Connection;

class Mysql
{
    protected $pdo;
    protected $transLevel = 0;
    protected $serverId;

    public function __construct($pdo, $serverId)
    {
        $this->pdo = $pdo;
        $this->serverId = $serverId;
    }
}