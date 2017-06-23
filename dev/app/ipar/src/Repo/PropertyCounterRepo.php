<?php
namespace Ipar\Repo;

class PropertyCounterRepo extends IparRepoBase
{
    public function countSolved()
    {
        return $this->db->select()
            ->from('property')
            ->where('ptype_id', '=', get_type_id('solved'))
            ->count();
    }

    public function countImproving()
    {
        return $this->db->select()
            ->from('property')
            ->where('ptype_id', '=', get_type_id('improving'))
            ->count();
    }
}
