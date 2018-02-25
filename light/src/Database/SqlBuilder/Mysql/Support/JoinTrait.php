<?php
namespace Light\Database\SqlBuilder\Mysql\Support;

trait JoinTrait
{
    protected $joins = [];

    public function leftJoin($table, $left, $operate, $right)
    {
        return $this->join('LEFT', $table, $left, $operate, $right);
    }

    public function rightJoin($table, $left, $operate, $right)
    {
        return $this->join('RIGHT', $table, $left, $operate, $right);
    }

    public function innerJoin($table, $left, $operate, $right)
    {
        return $this->join('INNER', $table, $left, $operate, $right);
    }

    public function buildJoinSql()
    {
        if (!$this->joins) {
            return '';
        }

        return ' ' . implode(' ', $this->joins);
    }

    protected function join($type, $table, $left, $operate, $right)
    {
        $arr = [];
        $arr[] = $type;
        $arr[] = 'JOIN';

        $arr[] = is_array($table) ? "`{$table[0]}` `{$table[1]}`" : $table;

        $arr[] = 'ON';
        $arr[] = is_array($left) ? "`{$left[0]}` `{$left[1]}`" : $left;
        $arr[] = $this->binder->toOp($operate);
        $arr[] = is_array($right) ? "`{$right[0]}` `{$right[1]}`" : $right;
        $this->joins[] = implode('', $arr);

        return $this;
    }
}
