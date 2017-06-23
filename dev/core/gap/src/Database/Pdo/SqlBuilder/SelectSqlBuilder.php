<?php
namespace Gap\Database\Pdo\SqlBuilder;

class SelectSqlBuilder extends SqlBuilderBase
{

    protected $dto;
    protected $is_fetch_assoc = false;

    public function setDto($dto_name)
    {
        $this->dto = dto_manager()->get($dto_name);
        return $this;
    }
    public function setDtoClass($dto_class)
    {
        $this->dto = $dto_class;
        return $this;
    }
    public function setFetchAssoc()
    {
        $this->is_fetch_assoc = true;
        return $this;
    }
    public function select(...$fields)
    {
        foreach ($fields as $field) {
            $this->field($field);
        }
        return $this;
    }
    public function fetchAll()
    {
        $this->sql = $this->buildSelectSql();

        $stmt = $this->adapter->prepare($this->sql);
        $this->helper->bindValuesToStmt($stmt);
        $this->helper->bindParamsToStmt($stmt);

        if ($this->dto) {
            $stmt->setDto($this->dto);
        }

        if ($this->is_fetch_assoc) {
            $stmt->setFetchAssoc();
        }

        if (!$stmt->execute()) {
            return false;
        }

        return $stmt->fetchAll();
    }
    public function fetchOne()
    {
        $this->limit(1);
        $this->offset(0);
        if ($rows = $this->fetchAll()) {
            return $rows[0];
        }
        return false;
    }
    public function count()
    {
        $this->limit(1);
        $this->offset(0);

        $this->sql = $this->buildCountSql();

        $stmt = $this->adapter->prepare($this->sql);
        $this->helper->bindValuesToStmt($stmt);
        $this->helper->bindParamsToStmt($stmt);

        if (!$stmt->execute()) {
            return false;
        }

        if ($rows = $stmt->fetchAll()) {
            $obj = $rows[0];
            return $obj->count;
        }
        return false;
    }

    public function buildSelectSql()
    {
        return "SELECT"
            . $this->buildFieldSql()
            . ' FROM' . $this->buildTableSql()
            . $this->buildJoinSql()
            . $this->buildWhereSql()
            . $this->buildGroupBySql()
            . $this->buildOrderBySql()
            . $this->buildLimitSql()
            . $this->buildOffsetSql();
    }

    public function buildCountSql()
    {
        return "SELECT"
            . ' count(1) `count`'
            . ' FROM' . $this->buildTableSql()
            . $this->buildJoinSql()
            . $this->buildWhereSql()
            . $this->buildOrderBySql()
            . $this->buildLimitSql()
            . $this->buildOffsetSql();
    }
}
