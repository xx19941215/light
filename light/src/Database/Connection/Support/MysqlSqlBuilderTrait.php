<?php
namespace Light\Database\Connection\Support;

use Light\Database\SqlBuilder\Mysql\DeleteSqlBuilder;
use Light\Database\SqlBuilder\Mysql\InsertSqlBuilder;
use Light\Database\SqlBuilder\Mysql\SelectSqlBuilder;
use Light\Database\SqlBuilder\Mysql\UpdateSqlBuilder;

trait MysqlSqlBuilderTrait
{
    public function select(...$fields)
    {
        $ssb = new SelectSqlBuilder($this);
        $ssb->fields($fields);
        return $ssb;
    }

    public function update(...$tables)
    {
        $usb = new UpdateSqlBuilder($this);
        $usb->tables($tables);
        return $usb;
    }

    public function insert(...$tables)
    {
        $isb = new InsertSqlBuilder($this);
        $isb->tables($tables);
        return $isb;
    }

    public function delete(...$aliases)
    {
        $dsb = new DeleteSqlBuilder($this);
        $dsb->deleteAliases($aliases);
        return $dsb;
    }
}
