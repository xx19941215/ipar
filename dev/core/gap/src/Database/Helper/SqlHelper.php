<?php

namespace Gap\Database\Helper;

class SqlHelper
{
    protected $used_params = [];
    protected $used_param_index = 1;
    protected $bind_values = [];
    protected $bind_params = [];

    protected static $ops = [
        '=' => 1, '<>' => 1, '>' => 1, '>=' => 1, '<' => 1, '<=' => 1, 'LIKE' => 1, 'IN' => 1
    ];

    public function toField($args)
    {
        if (is_string($args)) {
            return "`$args`";
        }

        if (!is_array($args)) {
            _debug('toField $args can only be string or array');
        }

        if (!isset($args[1])) {
            return "`{$args[0]}`";
        }

        if ($args[1] == '*') {
            return "`{$args[0]}`.*";
        }

        $field = "`{$args[0]}`.`{$args[1]}`";
        if (isset($args[2])) {
            $field .= " " . $args[2];
        }
        return $field;
    }

    public function toOp($op)
    {
        if (array_key_exists($op, self::$ops)) {
            return $op;
        }

        var_dump($op);
        _debug('todo');
    }

    public function toParam($input)
    {
        $param = is_array($input) ?
            ":{$input[0]}_{$input[1]}"
            :
            ":$input";

        if (isset($this->used_params[$param])) {
            $param = "{$param}_{$this->used_param_index}";
        }

        $this->used_params[$param] = 1;
        $this->used_param_index++;
        return $param;
    }

    public function bindValue($param, $value, $type = 'str')
    {
        $this->bind_values[] = ['param' => $param, 'value' => $value, 'type' => $type];
        return $this;
    }

    public function bindParam($param, $value, $type = 'str')
    {
        $this->bind_params[] = ['param' => $param, 'value' => $value, 'type' => $type];
        return $this;
    }

    public function bindValuesToStmt($stmt)
    {
        if ($this->bind_values) {
            foreach ($this->bind_values as $bind) {
                $stmt->bindValue($bind['param'], $bind['value'], $bind['type']);
            }
        }
    }

    public function bindParamsToStmt($stmt)
    {
        if ($this->bind_params) {
            foreach ($this->bind_params as $bind) {
                $stmt->bindParam($bind['param'], $bind['value'], $bind['type']);
            }
        }
    }

    public function getBindValues()
    {
        return $this->bind_values;
    }
}
