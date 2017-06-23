<?php
namespace Gap\I18n;

class TranslatorAdmin extends Translator
{
    protected $count_per_page = 30;
    protected $current_page = 1;
    protected $page_count;
    protected $item_count;
    protected $builder = null;

    public function setCurrentPage($page)
    {
        $page = (int) $page;
        $page = ($page > 1) ? $page : 1;
        $this->current_page = $page;
        return $this;
    }

    public function getCurrentPage()
    {
        return $this->current_page;
    }

    public function setCountPerPage($count = 30)
    {
        $this->count_per_page = $count;
        return $this;
    }
    public function getTranses($query = '', $locale_id = '')
    {
        $builder = $this->getBuilder($query, $locale_id);
        return $builder->fetchAll();
    }
    public function cacheFlushAll()
    {
        return $this->cache->flushAll();
    }
    public function getPageCount($query = '')
    {
        $this->page_count = ceil($this->getItemCount($query) / $this->count_per_page);
        return $this->page_count;
    }

    public function getItemCount($query = '')
    {
        $sql = 'SELECT COUNT(DISTINCT `str`) `count` FROM `trans`';
        if ($query) {
            $sql .= " WHERE `str` LIKE :query OR `trans` LIKE :query";
        }
        $stmt = $this->adapter->prepare($sql);
        $stmt->bindValue('query', "%$query%");
        if (!$stmt->execute()) {
            return 0;
        }
        if (!$rs = $stmt->fetchAll()) {
            return 0;
        }
        $obj = $rs[0];
        return (int) $obj->count;

    }
    public function getAllTranses($str)
    {
        if ($objs = $this->adapter->select('locale_id', 'trans')
            ->from($this->db_table)
            ->where('str', '=', $str)
            ->fetchAll()
        ) {
            $ts = [];
            foreach ($objs as $obj) {
                $ts[$obj->locale_id] = $obj->trans;
            }
            return $ts;
        }

        return [];
    }
    protected function getBuilder($query = '', $locale_id = '')
    {
        if ($this->builder) {
            return $this->builder;
        }

        $builder = $this->adapter->select()
            ->from($this->db_table);

        if ($query) {
            $builder
                ->startGroup()
                ->where('str', 'LIKE', "%{$query}%")
                ->orWhere('trans', 'LIKE', "%{$query}%")
                ->endGroup();
        }
        if ($locale_id) {
            $builder
                ->andWhere('locale_id', '=', $locale_id);
        }
        $builder->groupBy('str');
        $builder->orderBy('changed', 'DESC');
        $builder->limit($this->count_per_page);
        $builder->offset(($this->current_page - 1) * $this->count_per_page);
        $this->builder = $builder;

        return $this->builder;
    }
}
