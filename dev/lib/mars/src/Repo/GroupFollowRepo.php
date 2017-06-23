<?php
namespace Mars\Repo;

class GroupFollowRepo extends MarsRepoBase
{
    public function findGroupFollow($query = [])
    {
        $gid = prop($query, 'gid', 0);
        $uid = prop($query, 'uid', current_uid());
        if (!$gid) {
            return $this->packError('gid', 'empty');
        }
        return $this->db->select()
            ->from('group_follow')
            ->setDto('group_follow')
            ->where('gid', '=', $gid, 'int')
            ->andWhere('uid', '=', $uid, 'int')
            ->fetchOne();
    }
    public function follow($gid)
    {
        $this->db->beginTransaction();
        if (!$this->createGroupFollow($gid)) {
            $this->db->rollback();
            return $this->packError('group_follow', 'create-failed');
        }
        if (!$this->incrFollowingCount()) {
            $this->db->rollback();
            return $this->packError('group_follow', 'incr-following-count-failed');
        }
        $this->db->commit();
        return $this->packOk();
    }

    public function createGroupFollow($gid)
    {
        $uid = current_uid();
        return $this->db->insert('group_follow')
            ->value('uid', $uid, 'int')
            ->value('gid', $gid, 'int')
            ->execute();
    }

    public function incrFollowingCount($op = 1)
    {
        $uid = current_uid();
        $update = $this->db->update('user_analysis')
            ->incr('group_following_count', $op)
            ->where('uid', '=', $uid, 'int')
            ->execute();

        return $update;
    }

    public function unfollow($gid)
    {
        $this->db->beginTransaction();

        if (!$this->deleteGroupFollowByGid($gid)) {
            $this->db->rollback();
            return $this->packError('group-follow', 'delete-failed');
        }
        if (!$this->incrFollowingCount(-1)) {
            $this->db->rollback();
            return $this->packError('group-follow', 'decr-following-count-failed');
        }

        $this->db->commit();
        return $this->packOk();
    }

    public function deleteGroupFollowByGid($gid)
    {
        $uid = current_uid();

        return $this->db->delete()
            ->from('group_follow')
            ->where('uid', '=', $uid, 'int')
            ->andWhere('gid', '=', $gid, 'int')
            ->execute();
    }

    public function schGroupFollowSsb($query = [])
    {
        $gid = prop($query, 'gid', 0);
        $uid = prop($query, 'uid', 0);
        $ssb = $this->db->select()
            ->from('group_follow')
            ->setDto('group_follow');
        if ($gid) {
            $ssb->andWhere('gid', '=', $gid, 'int');
        }
        if ($uid) {
            $ssb->andWhere('uid', '=', $uid, 'int');
        }
        return $ssb;
    }


    public function fetchFollowedSet($gid)
    {
        return $this->dataSet($this->schGroupFollowSsb(['gid' => $gid ]));
    }

    public function fetchFollowingSet($uid = '')
    {
        return $this->dataSet($this->schGroupFollowSsb(['uid' => $uid ]));
    }
}
