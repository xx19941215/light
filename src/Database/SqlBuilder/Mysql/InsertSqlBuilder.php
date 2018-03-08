<?php
namespace Light\Database\SqlBuilder\Mysql;

class InsertSqlBuilder extends SqlBuilder
{
    protected $values = [];

    public function insert($table) : self
    {
        $this->table($table);
        return $this;
    }

    public function value($field, $value, $type = 'str'): self
    {
        $param = $this->binder->toParam($field);
        $this->field($field);

        $this->binder->bindValue($param, $value, $type);
        $this->values[] = $param;

        return $this;
    }

    public function values(array $values): self
    {
        foreach ($values as $key => $item) {
            $val = $item;
            $type = 'str';

            if (is_array($item) && isset($item[1])) {
                $val = $item[0];
                $type = $item[1];
            }

            $this->value($key, $val, $type);
        }

        return $this;
    }

    public function execute()
    {
        $stmt = $this->buildStmt($this->buildInsertSql());
        return $stmt->execute();
    }

    public function buildInsertSql(): string
    {
        $this->sql = "INSERT INTO" . implode(', ', $this->tables)
            . ' (' . implode(', ', $this->fields) . ')'
            . ' VALUES (' . implode(', ', $this->values) . ')';

        return $this->sql;
    }
}
