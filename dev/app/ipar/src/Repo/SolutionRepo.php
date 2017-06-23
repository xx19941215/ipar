<?php

namespace Ipar\Repo;

class SolutionRepo extends \Mars\Repo\EntityRepo
{
    public function ifVoted($solution_id, $uid)
    {
        $isVoted = $this->db->select()
            ->from('solution_vote')
            ->where('solution_id', '=', $solution_id, 'int')
            ->andWhere('uid','=',$uid,'int')
            ->andWhere('status','=',1,'int')
            ->fetchOne();
        return $isVoted;
    }
}
