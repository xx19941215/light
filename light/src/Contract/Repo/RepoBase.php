<?php
namespace Light\Contract\Repo;

use Light\Contract\Database\SqlBuilder\SelectSqlBuilderInterface;
use Light\Database\DatabaseManager;
use Light\Database\DateSet;

abstract class RepoBase
{
    protected $cnnName;
    protected $cnn;

    protected $filedTypeMap;
    protected $dmg;

    public function __construct(DatabaseManager $dmg)
    {
        $this->dmg = $dmg;
        if (empty($this->cnnName)) {
            throw new \Exception("cnnName could not be empty");
        }

        $this->cnn = $this->dmg->connect($this->cnnName);
    }

    protected function startup()
    {
    }

    protected function getFieldType($fieldName)
    {

    }

    protected function dataSet(SelectSqlBuilderInterface $ssb, $dtoClass) : DateSet
    {
        return new DateSet($ssb, $dtoClass);
    }
}