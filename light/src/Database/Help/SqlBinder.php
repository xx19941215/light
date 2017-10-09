<?php
namespace Light\Database\Help;

use Light\Database\Exception\DatabaseException;

class SqlBinder
{
    protected $usedParams = [];
    protected $usedParamIndex = 1;
    protected $bindValues = [];
    protected $bindParams = [];

    protected static $ops = [
        '='    => 1,
        '<>'   => 1,
        '>'    => 1,
        '>='   => 1,
        '<'    => 1,
        '<='   => 1,
        'LIKE' => 1,
        'IN'   =>1
    ];

    public function toField($args)
    {
        if (is_string($args)) {
            return "`$args`";
        }

        if (!is_array($args)) {
            throw new DatabaseException('toField $args can only be string or array');
        }

        if (!isset($args[1])) {
            return "`{$args[0]}`";
        }

        if ($args[1] == "*") {
            return "`{$args[0]}`.*";
        }

        $field = "`{$args[0]}`.`{$args[1]}`";
        if (isset($args[2])) {
            $field.= " " . $args[2];
        }

        return $field;
    }

    public function toOp($operate)
    {
        if (array_key_exists($operate, self::$ops)) {
            return $operate;
        }

        throw new DatabaseException("$operate not find");
    }

    public function toParam($input)
    {
        $param = is_array($input) ?
            ":{$input[0]}_{$input[1]}"
            :
            ":$input";

        if (isset($this->usedParams[$param])) {
            $param = "{$param}_{$this->usedParamIndex}";
        }

        $this->usedParams[$param] = 1;
        $this->usedParamIndex++;
        return $param;
    }

    public function bindValue($param, $value, $type = 'str') : SqlBinder
    {
        $this->bindValues[] = compact('param', 'value', 'type');
        return $this;
    }

    public function bindParam($param, $value, $type = 'str') : SqlBinder
    {
        $this->bindParams[] = compact('param', 'value', 'type');
        return $this;
    }

    public function getValues()
    {
        return $this->bindValues;
    }

    public function getParams()
    {
        return $this->bindParams;
    }
}