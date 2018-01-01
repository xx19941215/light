<?php
namespace Light\Database\SqlBuilder\Mysql\Support;

trait WhereTrait
{
    protected $wheres = [];

    public function where($field, $operate, $value, $type = 'str')
    {
       $param = $this->binder->toParam($field);
       $field = $this->binder->toField($field);
       $operate = $this->binder->toOp($operate);

       if ($operate === 'IN') {
           if (is_array($value)) {
               $index = 0;
               $param = [];
               foreach ($value as $sub) {
                   $newParam = "{$param}_{$index}";
                   $this->binder->bindValue($newParam, $sub, $type);
                   $param[] = $newParam;
                   $index++;
               }
           }

           $paramsStr = implode(', ', $param);
           $this->wheres[] = "{$field} IN ({$paramsStr})";
           return $this;
       }
       $this->wheres[] = "{$field} {$operate} {$param}";
       $this->binder->bindValue($param, $value, $type);
       return $this;
    }

    public function andWhere($field, $operate, $value, $type = 'str')
    {
       if ($this->wheres) {
           $this->wheres[] = 'AND';
       }
       $this->where($field, $operate, $value, $type);
       return $type;
    }

    public function orWhere($field, $operate, $value, $type = 'str')
    {
       if ($this->wheres) {
           $this->wheres[] = 'OR';
       }

       $this->where($field, $operate, $value, $type);
       return $type;
    }

    public function whereCond($field1, $operate, $field2)
    {
       $field1 = $this->binder->toField($field1);
       $field2 = $this->binder->toField($field2);
       $operate = $this->binder->toOp($operate);

       $this->wheres[] = "{$field1} {$operate} {$field2}";
       return $this;
    }

    public function andWhereCond($field1, $operate, $field2)
    {
       if ($this->wheres) {
           $this->wheres[] = 'AND';
       }

       $this->whereCond($field1, $operate, $field2);
       return $this;
    }

    public function orWhereCond($field1, $operate, $field2)
    {
       if ($this->wheres) {
           $this->wheres[] = 'OR';
       }

       $this->whereCond($field1, $operate, $field2);
       return $this;
    }

    public function whereRaw($sql)
    {
       $this->wheres[] = $sql;
       return $this;
    }

    public function andWhereRaw($sql)
    {
       if ($this->wheres) {
           $this->wheres[] = 'AND';
       }

       $this->wheres[] = $sql;
       return $this;
    }

    public function buildWhereSql()
    {
       if (!$this->wheres) {
           return '';
       }

       return ' where ' . implode(' ', $this->wheres);
    }

    public function startGroup($logic = '')
    {
       if ($this->wheres) {
           if ($logic === 'AND' || $logic = 'OR') {
               $this->wheres[] = $logic;
           }
       }

       $this->wheres[] = '{';
       return $this;
    }

    public function endGroup()
    {
       $this->wheres[] = ')';
       return $this;
    }
}