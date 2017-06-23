<?php
namespace Ipar\Search\DataSet;

class SearchDataSet
{
    protected $count_per_page = 10;
    protected $current_page = 1;

    protected $xs;
    protected $fetch_class;

    protected $docs;

    public function __construct($xs, $fetch_class)
    {
        $this->xs = $xs;
        $this->fetch_class = $fetch_class;
    }

    public function setCurrentPage($page)
    {
        $page = ($page > 1) ? $page : 1;
        $this->current_page = $page;
        return $this;
    }

    public function getCurrentPage()
    {
        return $this->current_page;
    }

    public function getItemCount()
    {
        $this->search();
        return $this->xs->count();
    }

    public function getPageCount()
    {
        return ceil($this->getItemCount() / $this->count_per_page);
    }

    public function getItems()
    {
        $docs = $this->search();
        $items = [];
        $class = $this->fetch_class;
        foreach ($this->search() as $doc) {
            $items[] = new $class($doc, $this->xs);
        }
        return $items;
    }

    public function setCountPerPage($count)
    {
        $this->count_per_page = $count;
        return $this;
    }

    public function clear()
    {
        $this->docs = null;
    }

    protected function search()
    {
        if ($this->docs) {
            return $this->docs;
        }

        $this->xs->setLimit(
            $this->count_per_page,
            ($this->getCurrentPage() - 1) * $this->count_per_page
        );
        $this->docs = $this->xs->search();

        return $this->docs;
    }
}
