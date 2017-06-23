<?php
namespace Ipar\Repo;

class ProductCounterRepo extends IparRepoBase
{
    public function countProduct()
    {
        return $this->db->select()
            ->from('product')
            ->count();
    }

}
