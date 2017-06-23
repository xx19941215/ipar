<?php
namespace Gap\Database\Pdo\SqlBuilder;

class UpdateSqlBuilder extends SqlBuilderBase
{

    protected $sets = [];

    public function update(...$tables)
    {
        foreach ($tables as $table) {
            if ($table) {
                $this->table($table);
            }
        }
        return $this;
    }
    public function set($field, $value, $type = 'str')
    {
        $param = $this->helper->toParam($field);
        $sql_field = $this->helper->toField($field);
        $this->sets[] = "$sql_field = $param";
        $this->helper->bindValue($param, $value, $type);
        return $this;
    }
    public function setRaw($field, $raw)
    {
        $sql_field = $this->helper->toField($field);
        $this->sets[] = "$sql_field = $raw";
        return $this;
    }
    public function incr($field, $rate = 1)
    {
        $sql_field = $this->helper->toField($field);
        $op = $rate > 0 ? '+' : '-';
        $rate = abs($rate);
        $this->sets[] = "$sql_field = $sql_field $op $rate";
        return $this;
    }
    public function sets(array $values)
    {
        foreach ($values as $key => $item) {
            $val = $item;
            $type = 'str';

            if (is_array($item) && isset($item[1])) {
                $val = $item[0];
                $type = $item[1];
            }

            $this->set($key, $val, $type);
        }
        return $this;
    }

    public function execute()
    {
        $this->sql = $this->buildUpdateSql();
        $stmt = $this->adapter->prepare($this->sql);
        $this->helper->bindValuesToStmt($stmt);
        $this->helper->bindParamsToStmt($stmt);
        return $stmt->execute();
    }

    public function buildUpdateSql()
    {
        return "UPDATE " . implode(', ', $this->tables)
            . $this->buildJoinSql()
            . ' SET ' . implode(', ', $this->sets)
            . $this->buildWhereSql();
    }
}
