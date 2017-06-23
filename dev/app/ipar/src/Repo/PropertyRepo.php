<?php

namespace Ipar\Repo;

class PropertyRepo extends \Mars\Repo\EntityRepo
{
    public function ifVoted($property_id, $uid)
    {
        $isVoted = $this->db->select()
            ->from('property_vote')
            ->where('property_id', '=', $property_id, 'int')
            ->andWhere('uid','=',$uid,'int')
            ->andWhere('status','=',1,'int')
            ->fetchOne();
        return $isVoted;
    }
}
