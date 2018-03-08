<?php
namespace Light\Database\SqlBuilder\Mysql\Support;

trait GroupTrait
{
    protected $groups = [];

    public function groupBy($field)
    {
        $group = $this->binder->toField($field);
        $this->groups[] = $group;
        return $this;
    }

    public function buildGroupBySql()
    {
        if (!$this->groups) {
            return '';
        }

        return ' GROUP BY ' . implode(', ', $this->groups);
    }
}
