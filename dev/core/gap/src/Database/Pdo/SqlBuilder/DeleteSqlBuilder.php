<?php
namespace Gap\Database\Pdo\SqlBuilder;

class DeleteSqlBuilder extends SqlBuilderBase
{

    private $delete_aliases = [];

    public function delete(...$aliases)
    {
        foreach ($aliases as $alias) {
            $this->deleteAlias($alias);
        }
    }

    public function deleteAlias($alias)
    {
        if ($alias = trim($alias)) {
            $this->delete_aliases[] = "`$alias`";
        }
    }

    public function buildAliasesSql()
    {
        if ($this->delete_aliases) {
            return implode(', ', $this->delete_aliases);
        }

        return implode(', ', $this->table_aliases);
    }

    public function execute()
    {
        $this->sql = $this->buildDeleteSql();

        //$sql = "DELETE FROM " . implode(', ', $this->tables)
        //    . $this->buildWhereSql();
        $stmt = $this->adapter->prepare($this->sql);
        $this->helper->bindValuesToStmt($stmt);
        $this->helper->bindParamsToStmt($stmt);
        return $stmt->execute();
    }

    public function buildDeleteSql()
    {
        return "DELETE " . $this->buildAliasesSql()
            . ' FROM' . $this->buildTableSql()
            . $this->buildJoinSql()
            . $this->buildWhereSql()
            . $this->buildOrderBySql();
            //. $this->buildLimitSql()
            //. $this->buildOffsetSql();
    }
}
