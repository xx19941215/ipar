<?php
namespace Gap\Database\Pdo\SqlBuilder\Support;

trait WhereTrait
{
    protected $wheres = [];

    public function where($field, $op, $value, $type = 'str')
    {
        $param = $this->helper->toParam($field);
        $field = $this->helper->toField($field);
        $op = $this->helper->toOp($op);
        if ($op === 'IN') {
            if (is_array($value)) {
                $index = 0;
                $params = [];
                foreach ($value as $sub) {
                    $new_param = "{$param}_{$index}";
                    $this->helper->bindValue($new_param, $sub, $type);
                    $params[] = $new_param;
                    $index++;
                }
            }
            $params_str = implode(', ', $params);
            $this->wheres[] = "{$field} IN ({$params_str})";
            return $this;
        }

        $this->wheres[] = "{$field} {$op} {$param}";
        $this->helper->bindValue($param, $value, $type);
        return $this;
    }

    public function andWhere($field, $op, $value, $type = 'str')
    {
        if ($this->wheres) {
            $this->wheres[] = 'AND';
        }
        $this->where($field, $op, $value, $type);
        return $this;
    }

    public function orWhere($field, $op, $value, $type = 'str')
    {
        if ($this->wheres) {
            $this->wheres[] = 'OR';
        }
        $this->where($field, $op, $value, $type);
        return $this;
    }

    public function whereRaw($sql)
    {
        $this->wheres[] = $sql;
        return $this;
    }

    public function andWhereRaw($sql)
    {
        if ($this->wheres) {
            $this->wheres[] = 'AND';
        }
        $this->wheres[] = $sql;
        return $this;
    }

    public function orWhereRaw($sql)
    {
        if ($this->wheres) {
            $this->wheres[] = 'OR';
        }
        $this->wheres[] = $sql;
        return $this;
    }

    // deprecated
    public function getWhereSet()
    {
        return $this->getWheres();
    }

    public function getWheres()
    {
        return $this->wheres;
    }

    public function buildWhereSql()
    {
        if (!$this->wheres) {
            return '';
        }

        return ' WHERE ' . implode(' ', $this->wheres);
    }

    public function startGroup($lg = '')
    {
        if ($this->wheres) {
            if ($lg === 'AND' || $lg === 'OR') {
                $this->wheres[] = $lg;
            }
        }
        $this->wheres[] = '(';
        return $this;
    }

    public function endGroup()
    {
        $this->wheres[] = ')';
        return $this;
    }
}
