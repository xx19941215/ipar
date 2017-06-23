<?php
namespace Mars\Repo;

class FollowRepo extends MarsRepoBase
{
    public function findFollowId($table, $col, $value)
    {
        $uid = current_uid();

        $Id = $this->db->select()
            ->from($table)
            ->where('uid', '=', $uid, 'int')
            ->andWhere($col, '=', $value, 'int')
            ->fetchOne();

        return !$Id?null:$Id->uid;
    }

    public function createFollow($table, $col, $value)
    {
        $uid = current_uid();

        $create = $this->db->insert($table)
            ->value('uid', $uid, 'int')
            ->value($col, $value, 'int')
            ->execute();
        
        return $create;
    }
    public function findAnalysisId($table, $col, $value)
    {

        $AnalysisId = $this->db->select()
            ->from($table)
            ->where($col, '=', $value, 'int')
            ->fetchOne();
            
        return !$AnalysisId?null:$AnalysisId->$col;
    }

    public function createUserAnalysisCount($table, $col)
    {
        $uid = current_uid();
        $create = $this->db->insert($table)
            ->value('uid', $uid, 'int')
            ->value($col, 1, 'int')
            ->execute();

        return $create;
    }
    public function createEntityAnalysisCount($table, $col)
    {
        $uid = current_uid();
        $create = $this->db->insert($table)
            ->value('eid', $uid, 'int')
            ->value($col, 1, 'int')
            ->execute();

        return $create;
    }
    public function updateUserAnalysisCount($table, $col, $value, $op)
    {
        $uid = current_uid();
        $update = $this->db->update($table)
            ->incr($col, $op)
            ->where('uid', '=', $value, 'int')
            ->execute();

        return $update;
    }
    public function updateEntityAnalysisCount($table, $col, $value, $op)
    {
        $uid = current_uid();
        $update = $this->db->update($table)
            ->incr($col, $op)
            ->where('eid', '=', $value, 'int')
            ->execute();

        return $update;
    }

    public function getFollowCount($table, $col, $value)
    {
        $AnalysisId = $this->db->select()
            ->from($table)
            ->where($col, '=', $value, 'int')
            ->fetchOne();
            
        return !$AnalysisId?null:$AnalysisId;
    }

    public function schFollowedListSet($table, $col, $value)
    {
        $ssb = $this->db->select()
            ->from($table)
            ->where($col, '=', $value, 'int')
            ->fetchAll();
        return $ssb;
    }

    public function deleteFollowed($table, $col, $value)
    {
        $uid = current_uid();
        $deleted = $this->db->delete()
            ->from($table)
            ->where('uid', '=', $uid, 'int')
            ->andWhere($col, '=', $value, 'int')
            ->execute();
        return $deleted;
    }
}
