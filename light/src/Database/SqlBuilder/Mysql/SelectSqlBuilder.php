<?php
namespace Light\Database\SqlBuilder\Mysql;

use Light\Contract\Database\SqlBuilder\SelectSqlBuilderInterface;
use Light\Database\Statement\Statement;

class SelectSqlBuilder extends SqlBuilder implements SelectSqlBuilderInterface
{
    public function listDto($dtoClass)
    {
        $stmt = $this->buildSelectStmt();
        $stmt->setFetchAssoc();
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            yield new $dtoClass($row);
        }
    }

    public function listAssoc() : array
    {
       $stmt = $this->buildSelectStmt();
       $stmt->setFetchAssoc();
       return $stmt->fetchAll();
    }

    public function listObj() : array
    {
        $stmt = $this->buildSelectStmt();
        $stmt->setFetchObj();
        return $stmt->fetchAll();
    }

    public function fetchDto($dtoClass)
    {
        if ($row = $this->fetchAssoc()) {
            return new $dtoClass($row);
        }

        return null;
    }

    public function count()
    {
        if ($this->groups) {
            $this->sql = "SELECT 1"
                . ' FROM' . $this->buildTableSql()
                . $this->buildJoinSql()
                . $this->buildWhereSql()
                . $this->buildGroupBySql();

            $stmt = $this->buildStmt($this->sql);
            $stmt->execute();
            return $stmt->rowCount();
        }

        $this->limit(1);
        $this->offset(0);
        $stmt = $this->buildStmt($this->buildCountSql());
        $stmt->setFetchObj();

        if ($obj = $stmt->fetchOne()) {
            return $obj->count;
        }

        return 0;
    }

    public function buildSelectSql()
    {
        $this->sql = "SELECT"
            . $this->buildFieldSql()
            . ' FROM' . $this->buildTableSql()
            . $this->buildJoinSql()
            . $this->buildWhereSql()
            . $this->buildGroupBySql()
            . $this->buildOrderBySql()
            . $this->buildLimitSql()
            . $this->buildOffsetSql();

        return $this->sql;
    }

    public function buildCountSql() : string
    {
        $this->sql = "SELECT"
            . ' count(1) `count`'
            . ' FROM' . $this->buildTableSql()
            . $this->buildJoinSql()
            . $this->buildWhereSql()
            . $this->buildLimitSql()
            . $this->buildOffsetSql();

        return $this->sql;
    }
    
    public function fetchAssoc() : array
    {
       $this->limit(1);
       $stmt = $this->buildSelectStmt();
       $stmt->setFetchAssoc();
       return $stmt->fetchOne();
    }

    public function fetchObj() : \stdClass
    {
       $this->limit(1);
       $stmt = $this->buildSelectStmt();
       $stmt->setFetchObj();
       return $stmt->fetchOne();
    }

    protected function buildSelectStmt() : Statement
    {
        return $this->buildStmt($this->buildSelectSql());
    }
}