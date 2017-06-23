<?php
namespace Mars\Repo;

class EntityFollowRepo extends MarsRepoBase
{
    public function findEntityFollow($query = [])
    {
        $ssb = $this->db->select()
            ->from('entity_follow')
            ->setDto('entity_follow');
            
        if ($uid = prop($query, 'uid')) {
            $ssb->andWhere('uid', '=', $uid, 'int');
        }

        if ($eid = prop($query, 'eid')) {
            $ssb->andWhere('eid', '=', $eid, 'int');
        }

        if (!$ssb->getWheres()) {
            return $this->packError('query', 'error-query');
        }

        return $ssb->setDto('entity_follow')
            ->fetchOne();

    }

    public function isFollowing($eid)
    {
        if ($this->findEntityFollow([
            'eid' => $eid,
            'uid' => current_uid()
        ])) {
            return true;
        }

        return false;
    }
    public function createEntityFollow($eid)
    {
        $uid = current_uid();

        return $this->db->insert('entity_follow')
            ->value('uid', $uid, 'int')
            ->value('eid', $eid, 'int')
            ->execute();
    }

    public function follow($eid)
    {
        $this->db->beginTransaction();

        if (!$this->createEntityFollow($eid)) {
            $this->db->rollback();
            return $this->packError('entity_follow', 'insert-failed');
        }
        if (!$this->incrFollowedCount($eid)) {
            $this->db->rollback();
            return $this->packError('follow', 'incr-insert-failed');
        }
        if (!$this->incrFollowingCount()) {
            $this->db->rollback();
            return $this->packError('follow', 'incr-insert-failed');
        }
        $this->db->commit();
        return $this->packOk();
    }
    public function deleteEntityFollow($eid)
    {
        $uid = current_uid();

        return $this->db->delete()
            ->from('entity_follow')
            ->where('uid', '=', $uid, 'int')
            ->andWhere('eid', '=', $eid, 'int')
            ->execute();
    }

    public function unfollow($eid)
    {
        $this->db->beginTransaction();

        if (!$this->deleteEntityFollow($eid)) {
            $this->db->rollback();
            return $this->packError('entity_unfollow', 'delete-failed');
        }
        if (!$this->incrFollowedCount($eid, -1)) {
            $this->db->rollback();
            return $this->packError('entity_unfollow', 'incr-insert-failed');
        }
        if (!$this->incrFollowingCount(-1)) {
            $this->db->rollback();
            return $this->packError('entity_unfollow', 'incr-insert-failed');
        }
        $this->db->commit();
        return $this->packOk();
    }
    public function findEntityAnalysis($eid)
    {
        $analysis_id = $this->db->select()
            ->from('entity_analysis')
            ->where('eid', '=', $eid, 'int')
            ->fetchOne();
            
        return $analysis_id;
    }

    public function updateEntityAnalysis($eid, $col, $op = 1)
    {
        $update = $this->db->update('entity_analysis')
            ->incr($col, $op)
            ->where('eid', '=', $eid, 'int')
            ->execute();

        return $update;
    }
    public function createEntityAnalysis($eid, $col)
    {
        $create = $this->db->insert('entity_analysis')
            ->value('eid', $eid, 'int')
            ->value($col, 1, 'int')
            ->execute();

        return $create;
    }
    public function incrFollowedCount($eid, $op = 1)
    {
        if ($this->findEntityAnalysis($eid)) {
            return $this->updateEntityAnalysis($eid, 'followed_count', $op);
        }
        return $this->createEntityAnalysis($eid, 'followed_count');
    }
    public function findUserAnalysis($uid)
    {
        $analysis_id = $this->db->select()
            ->from('user_analysis')
            ->where('uid', '=', $uid, 'int')
            ->fetchOne();
            
        return $analysis_id;
    }

    public function createUserAnalysis($uid, $col)
    {
        $create = $this->db->insert('user_analysis')
            ->value('uid', $uid, 'int')
            ->value($col, 1, 'int')
            ->execute();

        return $create;
    }

    public function updateUserAnalysis($dst_uid, $col, $op = 1)
    {
        $update = $this->db->update('user_analysis')
            ->incr($col, $op)
            ->where('uid', '=', $dst_uid, 'int')
            ->execute();

        return $update;
    }
    public function incrFollowingCount($op = 1)
    {
        $uid = current_uid();

        if ($this->findUserAnalysis($uid)) {
            return $this->updateUserAnalysis($uid, 'entity_following_count', $op);
        }
        return $this->createUserAnalysis($uid, 'entity_following_count');
    }
    public function countFollowed($eid)
    {
        $obj = $this->db->select()
            ->from('entity_analysis')
            ->where('eid', '=', $eid, 'int')
            ->fetchOne();
        return $obj ? $obj->followed_count : 0;
    }
    public function countFollowing($uid)
    {
        $obj = $this->db->select()
            ->from('user_analysis')
            ->where('uid', '=', $uid, 'int')
            ->fetchOne();
        return $obj ? $obj->entity_following_count : 0;
    }
    public function schEntityFollowSsb($query = [])
    {
        $ssb = $this->db->select()
            ->from('entity_follow')
            ->setDto('user_follow');
        if ($eid = prop($query, 'eid')) {
            $ssb->andWhere('eid', '=', $eid, 'int');
        }
        if ($uid = prop($query, 'uid')) {
            $ssb->andWhere('uid', '=', $uid, 'int');
        }

        $ssb->orderBy('created','desc');
        return $ssb;
    }

    public function fetchFollowedSet($eid)
    {
        return $this->dataSet(
            $this->schEntityFollowSsb(['eid' => $eid])
        )
        ->setCountPerpage(10);
    }

    public function fetchFollowingSet($uid)
    {
        return $this->dataSet(
            $this->schEntityFollowSsb(['uid' => $uid])
        )->setCountPerpage(10);
    }
}
