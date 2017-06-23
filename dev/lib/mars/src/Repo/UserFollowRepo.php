<?php
namespace Mars\Repo;

class UserFollowRepo extends MarsRepoBase
{
    public function findUserFollow($query = [])
    {
        $ssb = $this->db->select()
            ->from('user_follow');
        if ($uid = prop($query, 'uid')) {
            $ssb->andWhere('uid', '=', $uid, 'int');
        }

        if ($dst_uid = prop($query, 'dst_uid')) {
            $ssb->andWhere('dst_uid', '=', $dst_uid, 'int');
        }

        if (!$ssb->getWheres()) {
            return $this->packError('query', 'error-query');
        }

        return $ssb->setDto('user_follow')
            ->fetchOne();

    }

    public function isFollowing($dst_uid)
    {
        if ($this->findUserFollow([
            'dst_uid' => $dst_uid,
            'uid' => current_uid()
        ])
        ) {
            return true;
        }

        return false;
    }

    public function createUserFollow($dst_uid)
    {
        $uid = current_uid();

        return $this->db->insert('user_follow')
            ->value('uid', $uid, 'int')
            ->value('dst_uid', $dst_uid, 'int')
            ->execute();
    }

    public function follow($dst_uid)
    {
        $this->db->beginTransaction();

        if (!$this->createUserFollow($dst_uid)) {
            $this->db->rollback();
            return $this->packError('user_follow', 'insert-failed');
        }
        if (!$this->incrFollowedCount($dst_uid)) {
            $this->db->rollback();
            return $this->packError('follow', 'incr-insert-failed');
        }
        if (!$this->incrFollowingCount($dst_uid)) {
            $this->db->rollback();
            return $this->packError('follow', 'incr-insert-failed');
        }

        $this->db->commit();
        return $this->packOk();
    }

    public function deleteUserFollow($dst_uid)
    {
        $uid = current_uid();

        return $this->db->delete()
            ->from('user_follow')
            ->where('uid', '=', $uid, 'int')
            ->andWhere('dst_uid', '=', $dst_uid, 'int')
            ->execute();
    }

    public function unfollow($dst_uid)
    {
        $this->db->beginTransaction();

        if (!$this->deleteUserFollow($dst_uid)) {
            $this->db->rollback();
            return $this->packError('user_unfollow', 'delete-failed');
        }
        if (!$this->incrFollowedCount($dst_uid, -1)) {
            $this->db->rollback();
            return $this->packError('follow', 'incr-insert-failed');
        }
        if (!$this->incrFollowingCount($dst_uid, -1)) {
            $this->db->rollback();
            return $this->packError('follow', 'incr-insert-failed');
        }

        $this->db->commit();
        return $this->packOk();
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

    public function incrFollowedCount($dst_uid, $op = 1)
    {
        if ($this->findUserAnalysis($dst_uid)) {
            return $this->updateUserAnalysis($dst_uid, 'followed_count', $op);
        }
        return $this->createUserAnalysis($dst_uid, 'followed_count');
    }

    public function incrFollowingCount($dst_uid, $op = 1)
    {
        $uid = current_uid();

        if ($this->findUserAnalysis($dst_uid)) {
            return $this->updateUserAnalysis($uid, 'following_count', $op);
        }
        return $this->createUserAnalysis($uid, 'following_count');
    }

    public function countFollowed($dst_uid)
    {
        $obj = $this->db->select()
            ->from('user_analysis')
            ->where('uid', '=', $dst_uid, 'int')
            ->fetchOne();

        return $obj ? $obj->followed_count : 0;
    }

    public function countFollowing($dst_uid)
    {
        $obj = $this->db->select()
            ->from('user_analysis')
            ->where('uid', '=', $dst_uid, 'int')
            ->fetchOne();
        return $obj ? $obj->following_count : 0;
    }

    public function schUserFollowSsb($query = [])
    {
        $ssb = $this->db->select()
            ->from('user_follow')
            ->setDto('user_follow');
        if ($uid = prop($query, 'uid')) {
            $ssb->andWhere('uid', '=', $uid, 'int');
        }

        if ($dst_uid = prop($query, 'dst_uid')) {
            $ssb->andWhere('dst_uid', '=', $dst_uid, 'int');
        }

        return $ssb;
    }

    public function schCommonUserSsb($query = [])
    {
        $ssb = $this->db->select(
            ['dst', 'uid']
        )
            ->from(['user_follow', 'dst'])
            ->setDto('user_follow')
            ->innerJoin(
                ['user_follow', 'crt'],
                ['dst', 'uid'],
                '=',
                ['crt', 'dst_uid']
            );

        $ssb->andWhere(['crt', 'uid'], '=', current_uid(), 'int');

        if ($dst_uid = prop($query, 'dst_uid')) {
            $ssb->andWhere(['dst', 'dst_uid'], '=', $dst_uid, 'int');
        }
        return $ssb;
    }


    public function fetchFollowedSet($dst_uid)
    {
        return $this->dataSet(
            $this->schUserFollowSsb([
                'dst_uid' => $dst_uid
            ])
        );
    }

    public function fetchFollowingSet($uid)
    {
        return $this->dataSet(
            $this->schUserFollowSsb([
                'uid' => $uid
            ])
        );
    }

    public function fetchCommonUserSet($dst_uid)
    {
        return $this->dataSet(
            $this->schCommonUserSsb([
                'dst_uid' => $dst_uid
            ])
        );
    }

    public function fetchPopularUsers($count = 10)
    {
        $sql = "SELECT user_follow.dst_uid,count(*) follow_count, " .
            "user.nick, user.avt, user.zcode FROM ideapar.user_follow LEFT JOIN " .
            "ideapar.user ON user.uid = user_follow.dst_uid GROUP BY " .
            "user_follow.dst_uid ORDER BY count(*) DESC LIMIT $count";
        return $this->db->query($sql)->fetchAll();
    }

}
