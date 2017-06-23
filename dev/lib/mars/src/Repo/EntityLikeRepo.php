<?php

namespace Mars\Repo;

class EntityLikeRepo extends MarsRepoBase
{
    public function like($eid)
    {
        $this->db->beginTransaction();

        $create = $this->createEntityLike(['eid' => $eid]);
        if (!$create->isOk()) {
            $this->db->rollback();
            return $create;
        }

        $incr = $this->incrLikeCount(['eid' => $eid]);
        if (!$incr->isOk()) {
            $this->db->rollback();
            return $incr;
        }

        $this->db->commit();
        return $this->packOk();
    }

    public function unlike($eid)
    {
        $this->db->beginTransaction();
        $delete = $this->deleteEntityLike(['eid' => $eid]);
        if (!$delete->isOk()) {
            $this->db->rollback();
            return $delete;
        }

        $incr = $this->incrLikeCount(['eid' => $eid, 'op' => -1]);
        if (!$incr->isOk()) {
            $this->db->rollback();
            return $incr;
        }

        $this->db->commit();
        return $this->packOk();
    }

    public function findEntityLike($query = [])
    {
        $eid = prop($query, 'eid', 0);
        $uid = prop($query, 'uid', current_uid());
        if ($eid <= 0) {
            return $this->packError('eid', 'empty||not-positive');
        }
        return $this->db->select()
            ->from('entity_like')
            ->where('eid', '=', $eid, 'int')
            ->andWhere('uid', '=', $uid, 'int')
            ->fetchOne();
    }
    public function deleteEntityLike($data = [])
    {
        $eid = prop($data, 'eid', 0);
        $uid = prop($data, 'uid', current_uid());
        if ($eid <= 0) {
            return $this->packError('eid', 'empty||not-positive');
        }
        $find = $this->findEntityLike(['eid' => $eid, 'uid' => $uid]);
        if (!$find) {
            return $find;
        }
        $delete = $this->db->delete()
            ->from('entity_like')
            ->where('eid', '=', $eid, 'int')
            ->andWhere('uid', '=', $uid, 'int')
            ->execute();
        if ($delete) {
            return $this->packOk();
        }
        return $this->packError('entity_like', 'delete-failed');

    }

    public function createEntityLike($data = [])
    {
        $eid = prop($data, 'eid', 0);
        $uid = prop($data, 'uid', current_uid());
        if ($eid <= 0) {
            return $this->packError('eid', 'empty || not-positive');
        }
        $find = $this->findEntityLike(['eid' => $eid, 'uid' => $uid]);
        if ($find) {
            return $this->packError('entity_like', 'had-exists');
        }
        $create = $this->db->insert('entity_like')
            ->value('eid', $eid, 'int')
            ->value('uid', $uid, 'int')
            ->execute();
        if ($create) {
            return $this->packOk();
        }
        return $this->packError('entity_like', 'insert-failed');
    }

    public function findEntityAnalysis($eid)
    {
        if (!$eid) {
            return $this->packError('eid', 'empty');
        }
        $find = $this->db->select()
            ->from('entity_analysis')
            ->where('eid', '=', $eid, 'int')
            ->fetchOne();
        if ($find) {
            return $this->packItems('entity_analysis', $find);
        }
        return $this->packError('eid', 'not-found');
    }

    public function incrLikeCount($query = [])
    {
        $eid = prop($query, 'eid', 0);
        $uid = prop($query, 'uid', current_uid());
        $op = prop($query, 'op', 1);

        $incr_user_analysis = $this->db->update('user_analysis')
            ->incr('entity_like_count', $op)
            ->where('uid', '=', $uid, 'int')
            ->execute();

        if (!$incr_user_analysis) {
            return $this->packError('user_analysis', 'update-failed');
        }
        if ($this->findEntityAnalysis($eid)->isOk()) {
            $incr_entity_analysis = $this->db->update('entity_analysis')
                ->incr('like_count', $op)
                ->where('eid', '=', $eid, 'int')
                ->execute();
        } else {
            $incr_entity_analysis = $this->db->insert('entity_analysis')
                ->value('eid', $eid, 'int')
                ->value('like_count', 1, 'int')
                ->execute();
        }
        if ($incr_entity_analysis) {
            return $this->packOk();
        }

        return $this->packError('entity_analysis', 'incr-failed');

    }

    public function schEntityLikeSsb($query = [])
    {
        $eid = prop($query, 'eid', 0);
        if (!$eid) {
            return $this->packError('eid', 'empty');
        }
        $ssb = $this->db->select()
            ->from('entity_like')
            ->setDto('entity_like')
            ->where('eid', '=', $eid, 'int');

        $ssb->orderBy('created','desc');
        return $ssb;
    }

    public function schEntityLikeSet($query = [])
    {
        return $this->DataSet($this->schEntityLikeSsb($query));
    }
}
