<?php
namespace Gap\Database\Pdo\SqlBuilder\Support;

trait GroupTrait
{
    protected $groups = [];

    public function groupBy($field)
    {
        $group = $this->helper->toField($field);
        $this->groups[] = $group;
        return $this;
    }

    public function buildGroupBySql()
    {
        if (!$this->groups) {
            return '';
        }
        return ' GROUP BY ' . implode(', ', $this->groups);
    }
}
