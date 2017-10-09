<?php
namespace Light\Database\Statement;

use Light\Database\Exception\DatabaseException;

class Statement
{
    protected static $dataTypeMaps = [
        'bool' => \PDO::PARAM_BOOL,
        'int'  => \PDO::PARAM_INT,
        'str'  => \PDO::PARAM_STR,
        'null' => \PDO::PARAM_NULL
    ];

    protected $stmt;

    public function __construct(\PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    public function setFetchAssoc() : Statement
    {
        $this->stmt->setFetchMode(\PDO::FETCH_ASSOC);
        return $this;
    }

    public function setFetchDto($dtoClass) : Statement
    {
        if (!class_exists($dtoClass)) {
            throw new DatabaseException("cannot find dto class $dtoClass");
        }

        $this->stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $dtoClass);
        return $this;
    }

    public function setFetchObj() : Statement
    {
        $this->stmt->setFetchMode(\PDO::FETCH_OBJ);
        return $this;
    }

    public function bindValue($param, $value, $typeName = 'str') : Statement
    {
        $typeValue = self::$dataTypeMaps[$typeName] ?? self::$dataTypeMaps['str'];
        $this->stmt->bindValue($param, $value, $typeValue);
        return $this;
    }

    public function bindParam($param, $value, $typeName = 'str') : Statement
    {
        $typeValue = self::$dataTypeMaps[$typeName] ?? self::$dataTypeMaps['str'];
        $this->stmt->bindParam($param, $value, $typeValue);
        return $this;
    }

    public function bindValues(array $values) : Statement
    {
        foreach ($values as $val) {
            $this->bindValue($val['param'], $val['value'], $val['type'] ?? 'str');
        }

        return $this;
    }

    public function bindParams(array $params) : Statement
    {
        foreach ($params as $val) {
            $this->bindParam($val['param'], $val['value'], $val['type'] ?? 'str');
        }

        return $this;
    }

    public function execute($params = []) : bool
    {
        $executed = $params ?
            $this->stmt->execute($params)
            :
            $this->stmt->execute();

        if (!$executed) {
            throw new DatabaseException("statement-execute-failed");
        }
    }

    public function fetch()
    {
        $this->execute();
        return $this->stmt->fetch();
    }

    public function fetchAll()
    {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    public function fetchOne()
    {
        $row = $this->fetchAll();
        if ($row) {
            return $row[0];
        }
    }

    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
}