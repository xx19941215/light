<?php
namespace Light\Database\SqlBuilder\Mysql;

use Light\Database\Help\SqlBinder;
use Light\Database\SqlBuilder\Mysql\Support\FieldTrait;
use Light\Database\SqlBuilder\Mysql\Support\GroupTrait;
use Light\Database\SqlBuilder\Mysql\Support\JoinTrait;
use Light\Database\SqlBuilder\Mysql\Support\OrderTrait;
use Light\Database\SqlBuilder\Mysql\Support\TableTrait;
use Light\Database\SqlBuilder\Mysql\Support\WhereTrait;
use Light\Database\Statement\Statement;

class SqlBuilder
{
    use WhereTrait;
    use JoinTrait;
    use OrderTrait;
    use GroupTrait;
    use FieldTrait;
    use TableTrait;

    protected $adapter;
    protected $binder;
    protected $sql;
    protected $limit = 30;
    protected $offset = 0;

    public function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->binder = new \Light\Database\Help\SqlBinder();
    }

    public function limit($limit): SqlBuilder
    {
        $this->limit = (int)$limit;
        return $this;
    }

    public function offset($offset): SqlBuilder
    {
        $this->offset = (int)$offset;
        return $this;
    }

    public function getExecutedSql(): string
    {
        return $this->sql;
    }

    public function getBinder(): SqlBinder
    {
        return $this->binder;
    }

    protected function buildLimitSql(): string
    {
        if (!$this->limit) {
            return '';
        }

        return " LIMIT {$this->limit}";
    }

    protected function buildOffsetSql(): string
    {
        if (!$this->offset) {
            return '';
        }

        return " OFFSET {$this->offset}";
    }

    protected function buildStmt($sql) : Statement
    {
       $stmt = $this->adapter->prepare($sql);
       $stmt->bindValues($this->binder->getValues());
       $stmt->bindParams($this->binder->getParams());

       return $stmt;
    }

}