<?php
namespace Gap\Database\Pdo\SqlBuilder\Support;

trait OrderTrait
{
    protected $orders = [];

    public function orderBy($field, $order = null)
    {
        $sort = $this->helper->toField($field);
        if ($order) {
            $sort .= ' ' . $order;
        }
        $this->orders[] = $sort;
        return $this;
    }

    public function orderByRaw($sql)
    {
        $this->orders[] = $sql;
        return $this;
    }

    public function buildOrderBySql()
    {
        if (!$this->orders) {
            return '';
        }
        return ' ORDER BY ' . implode(', ', $this->orders);
    }
}
