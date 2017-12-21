<?php
namespace Light\Database\SqlBuilder\Mysql\Support;

trait FieldTrait
{
    protected $fields = [];

    public function rawField($field)
    {
        $this->fields[] = $field;
        return $this;
    }

    public function field($args)
    {
        if ($args) {
            $this->fields[] = $this->binder->toField($args);
        }

        return $this;
    }

    public function fields($fields)
    {
        foreach ($fields as $field) {
            $this->field($field);
        }

        return $this;
    }

    public function buildFieldSql()
    {
        if (!$this->fields) {
            return " *";
        }
        return " " . implode(', ', $this->fields);
    }
}