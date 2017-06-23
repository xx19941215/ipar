<?php
namespace Gap\Database\Pdo\SqlBuilder\Support;

trait TableTrait
{
    protected $tables = [];
    protected $table_aliases = [];

    public function from(...$tables)
    {
        foreach ($tables as $table) {
            if ($table) {
                $this->table($table);
            }
        }
        return $this;
    }

    public function table($table)
    {
        if (is_array($table)) {
            $this->tables[] = "`{$table[0]}` `{$table[1]}`";
            $this->table_aliases[] = "`{$table[1]}`";
            return;
        }

        $this->tables[] = "`$table`";
        $this->table_aliases[] = "`$table`";
    }

    public function tables(...$tables)
    {
        foreach ($tables as $table) {
            $this->table($table);
        }
        return $this;
    }

    public function buildTableSql()
    {
        if (!$this->tables) {
            return '';
        }
        return ' ' . implode(', ', $this->tables);
    }
}
