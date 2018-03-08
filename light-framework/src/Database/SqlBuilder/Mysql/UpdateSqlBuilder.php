<?php
namespace Light\Database\SqlBuilder\Mysql;

class UpdateSqlBuilder extends SqlBuilder
{
    protected $sets = [];

    public function set($field, $value, $type = 'str') : self
    {
        $param = $this->binder->toParam($field);
        $sqlField = $this->binder->toField($field);

        $this->sets[] = "$sqlField = $param";
        $this->binder->bindValue($param, $value, $type);
        return $this;
    }

    public function setRaw($field, $raw) : self
    {
        $sqlField = $this->binder->toField($field);
        $this->sets[] = "$sqlField = $raw";
        return $this;
    }

    public function increment($filed, $rate = 1) : self
    {
        $sqlField = $this->binder->toField($filed);
        $operate = $rate > 0 ? '+' : '-';
        $rate = abs($rate);
        $this->sets[] = "$sqlField = $sqlField $operate $rate";
        return $this;
    }

    public function sets(array $values) : self
    {
        foreach ($values as $key => $item) {
            $val = $item;
            $type = 'str';
            
            if (is_array($item) && isset($item[1])) {
                $val = $item[0];
                $type = $item[1];
            }
            
            $this->set($key, $val, $type);
        }
        
        return $this;
    }

    public function execute() : bool
    {
        $stmt = $this->buildStmt($this->buildUpdateSql());
        return $stmt->execute();
    }

    public function buildUpdateSql() : string
    {
        $this->sql = "UPDATE " . implode(', ', $this->tables)
            . $this->buildJoinSql()
            . ' SET' . implode(', ', $this->sets)
            . $this->buildWhereSql();

        return $this->sql;
    }
}
