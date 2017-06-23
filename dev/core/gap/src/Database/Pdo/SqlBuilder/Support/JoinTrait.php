<?php
namespace Gap\Database\Pdo\SqlBuilder\Support;

trait JoinTrait
{
    protected $joins = [];

    public function leftJoin($table, $left, $op, $right)
    {
        return $this->join('LEFT', $table, $left, $op, $right);
    }

    public function rightJoin($table, $left, $op, $right)
    {
        return $this->join('RIGHT', $table, $left, $op, $right);
    }

    public function innerJoin($table, $left, $op, $right)
    {
        return $this->join('INNER', $table, $left, $op, $right);
    }

    public function buildJoinSql()
    {
        if (!$this->joins) {
            return '';
        }
        return ' ' . implode(' ', $this->joins);
    }

    protected function join($type, $table, $left, $op, $right)
    {
        $arr = [];
        $arr[] = $type;
        $arr[] = 'JOIN';
        if (is_array($table)) {
            $arr[] = "`{$table[0]}` `{$table[1]}`";
        } else {
            $arr[] = table;
        }
        $arr[] = 'ON';
        if (is_array($left)) {
            $arr[] = "`{$left[0]}`.`{$left[1]}`";
        } else {
            $arr[] = $left;
        }
        $arr[] = $this->helper->toOp($op);
        if (is_array($right)) {
            $arr[] = "`{$right[0]}`.`{$right[1]}`";
        } else {
            $arr[] = $right;
        }
        $this->joins[] = implode(' ', $arr);
        return $this;
    }
}
