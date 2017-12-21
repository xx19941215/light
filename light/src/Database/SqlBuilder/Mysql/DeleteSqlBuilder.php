<?php
namespace Light\Database\SqlBuilder\Mysql;

class DeleteSqlBuilder extends SqlBuilder
{
    private $deleteAliases = [];

    public function deleteAliases($aliases)
    {
        foreach ($aliases as $alias) {
            $this->deleteAlias($alias);
        }
    }

    public function deleteAlias($alias)
    {
        if ($alias = trim($alias)) {
            $this->deleteAliases[] = "`$alias`";
        }
    }
}