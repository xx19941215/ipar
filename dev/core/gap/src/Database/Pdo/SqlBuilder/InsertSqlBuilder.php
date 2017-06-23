<?php
namespace Gap\Database\Pdo\SqlBuilder;

class InsertSqlBuilder extends SqlBuilderBase
{

    protected $values = [];

    public function insert($table)
    {
        $this->table($table);
    }

    public function into($table)
    {
        $this->table($table);
        return $this;
    }

    public function value($field, $value, $type = 'str')
    {
        $param = $this->helper->toParam($field);

        $this->field($field);
        $this->helper->bindValue($param, $value, $type);
        $this->values[] = $param;

        return $this;
    }

    public function values(array $values)
    {
        foreach ($values as $key => $item) {
            $val = $item;
            $type = 'str';
            if (is_array($val) && isset($val[1])) {
                $val = $item[0];
                $type = $item['str'];
            }
            $this->value($key, $val[0], $val[1]);
        }
        return $this;
    }

    public function execute()
    {

        $this->sql = $this->buildInsertSql();
        $stmt = $this->adapter->prepare($this->sql);
        $this->helper->bindValuesToStmt($stmt);
        $this->helper->bindParamsToStmt($stmt);

        return $stmt->execute();
    }

    public function buildInsertSql()
    {
        return "INSERT INTO " . implode(', ', $this->tables)
            . ' (' . implode(', ', $this->fields) . ')'
            . ' VALUES (' . implode(', ', $this->values) . ')';
    }
}
