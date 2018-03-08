<?php
namespace Light\Contract\Database\SqlBuilder;

interface SelectSqlBuilderInterface
{
    public function count();
    public function listModel($modelClass);
    public function fetchModel($modelClass);
    public function listObj();
    public function fetchObjOne();
    public function listAssoc();
    public function fetchAssoc();
    public function buildSelectSql();
    public function buildCountSql();
}
