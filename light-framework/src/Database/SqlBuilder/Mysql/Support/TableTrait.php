<?php
namespace Light\Database\SqlBuilder\Mysql\Support;

trait TableTrait
{
    protected $tables = [];
    protected $tableAliases = [];

    public function table($table)
    {
        if (is_array($table)) {
            $this->tables[] = "`{$table[0]}` `{$table[1]}`";
            $this->tableAliases[] = "`{$table[1]}`";
            return;
        }

        $this->tables[] = "`$table`";
        $this->tableAliases[] = "`$table`";
    }

    public function tables($tables)
    {
        foreach ($tables as $table) {
            $this->table($table);
        }

        return $this;
    }

    public function from(...$tables)
    {
        $this->tables($tables);
        return $this;
    }

    protected function buildTableSql()
    {
        if (!$this->tables) {
            return '';
        }

        return ' ' . implode(', ', $this->tables);
    }
}
