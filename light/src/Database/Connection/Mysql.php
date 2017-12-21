<?php
namespace Light\Database\Connection;

use Light\Database\Connection\Support\MysqlSqlBuilderTrait;
use Light\Database\Exception\DatabaseException;
use Light\Database\Statement\Statement;

class Mysql
{
    use MysqlSqlBuilderTrait;

    protected $pdo;
    protected $transLevel = 0;
    protected $serverId;

    public function __construct(\PDO $pdo, $serverId)
    {
        $this->pdo = $pdo;
        $this->serverId = $serverId;
    }

    public function prepare($sql) : Statement
    {
        return new Statement($this->pdo->prepare($sql));
    }

    public function query($sql) : Statement
    {
        return new Statement($this->pdo->query($sql));
    }

    public function exec($sql)
    {
        return $this->pdo->exec($sql);
    }

    public function beginTransaction()
    {
        $this->transLevel++;
        if ($this->transLevel > 1) {
            return;
        }

        if (!$this->pdo->beginTransaction()) {
            throw new DatabaseException("DB::beginTransaction failed");
        }
    }

    public function commit()
    {
        if ($this->transLevel <= 0) {
            $this->transLevel = 0;
            throw new DatabaseException("DB-commit-failed");
        }

        $this->transLevel--;
        if ($this->transLevel > 0) {
            return;
        }

        if (!$this->pdo->commit()) {
            throw new DatabaseException('db-commit-failed');
        }
    }

    public function rollback()
    {
        if ($this->transLevel === 0) {
            return;
        }

        $this->transLevel = 0;
        if (!$this->pdo->rollback()) {
            throw new DatabaseException('db-rollback-failed');
        }
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}