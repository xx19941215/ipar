<?php
namespace Gap\DataBase;

class DataSet
{

    protected $select;
    protected $data_callback;
    protected $count_callback;

    protected $item_count;
    protected $page_count;

    protected $count_per_page = 10;
    protected $current_page = null;

    public function __construct($select, $data_callback = null, $count_callback = null)
    {
        $this->select = $select;
        $this->data_callback = $data_callback;
        $this->count_callback = $count_callback;
    }

    public function setCurrentPage($page)
    {
        $page = ($page > 1) ? $page : 1;
        $this->current_page = $page;
        return $this;
    }

    public function setDataCallback($data_callback)
    {
        $this->data_callback = $data_callback;
        return $this;
    }

    public function setCountCallback($count_callback)
    {
        $this->count_callback = $count_callback;
        return $this;
    }

    public function getCurrentPage()
    {
        if ($this->current_page) {
            return $this->current_page;
        }

        $this->setCurrentPage(1);
        return $this->current_page;
    }

    public function getItemCount()
    {
        if ($this->item_count) {
            return $this->item_count;
        }

        if (!$this->count_callback) {
            $this->item_count = $this->select->count();
            return $this->item_count;
        }

        if (is_array($this->count_callback)) {
            list($service, $action) = $this->count_callback;
            $args = isset($this->count_callbac[2]) ? $this->count_callback[2] : null;

            $this->item_count = $args ?
                call_user_func_array([service($service), $action], $args)
                :
                $this->item_count = service($service)->$action();

            return $this->item_count;
        }

        return 0;
    }

    public function getPageCount()
    {
        if ($this->page_count) {
            return $this->page_count;
        }

        $this->page_count = ceil($this->getItemCount() / $this->count_per_page);
        return $this->page_count;
    }

    public function getItems()
    {
        $items = $this->select
            ->limit($this->count_per_page)
            ->offset(($this->getCurrentPage() - 1) * $this->count_per_page)
            ->fetchAll();

        if (!$this->data_callback) {
            return $items;
        }

        list($service, $action) = $this->data_callback;
        return service($service)->$action($items);
    }

    public function setCountPerPage($count)
    {
        $this->count_per_page = $count;
        return $this;
    }
}
