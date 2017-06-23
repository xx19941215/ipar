<?php

namespace Mars\Repo;

class PropertyVoteRepo extends MarsRepoBase
{
    public function vote($property_id)
    {
        $this->db->beginTransaction();

        $create = $this->createPropertyVote(['property_id' => $property_id]);
        if (!$create->isOk()) {
            $this->db->rollback();
            return $create;
        }

        $incr = $this->incrPropertyAnalysis(['property_id' => $property_id]);
        if (!$incr->isOk()) {
            $this->db->rollback();
            return $incr;
        }

        $this->db->commit();
        return $this->packItem('dst_eid', $this->findPropertyDstEid($property_id));
    }

    public function createPropertyVote($data = [])
    {
        $property_id = prop($data, 'property_id', 0);
        $uid = prop($data, 'uid', current_uid());
        if ($property_id <= 0) {
            return $this->packError('property_id', 'not-positive');
        }
        if ($this->findPropertyVote(['property_id' => $property_id])) {
            return $this->packError('property_vote', 'had-exist');
        }
        $create = $this->db->insert('property_vote')
            ->value('property_id', $property_id)
            ->value('uid', $uid)
            ->value('status', 1)
            ->execute();

        if ($create) {
            return $this->packOk();
        }
        return $this->packError('property_vote', 'insert-failed');
    }

    public function unvote($property_id)
    {
        $this->db->beginTransaction();

        $delete = $this->deletePropertyVote(['property_id' => $property_id]);
        if (!$delete->isOk()) {
            $this->db->rollback();
            return $delete;
        }
        // $update = $this->updatePropertyAnalysis(['property_id' => $property_id, 'op' => -1]);
        // if (!$update->isOk()) {
        //     $this->db->rollback();
        //     return $update;
        // }
        $this->db->commit();
        return $this->packItem('dst_eid', $this->findPropertyDstEid($property_id));
    }

    public function deletePropertyVote($query = [])
    {
        $property_id = prop($query, 'property_id', 0);
        $uid = prop($query, 'uid', current_uid());
        if ($property_id <= 0) {
            return $this->packError('property_id', 'not-positive');
        }

        if (!is_object($this->findPropertyVote(['property_id' => $property_id]))) {
            return $this->packError('property_vote', 'not-found');
        }

        $delete = $this->db->delete()
            ->from('property_vote')
            ->where('property_id', '=', $property_id, 'int')
            ->andWhere('uid', '=', $uid, 'int')
            ->execute();


        if ($delete) {
            return $this->packOk();
        }
        return $this->packError('property', 'insert-failed');
    }

    public function incrPropertyAnalysis($data = [])
    {
        $property_id = prop($data, 'property_id', 0);

        $property_analysis = $this->db->select()
            ->from('property_analysis')
            ->where('property_id', '=', $property_id, 'int')
            ->fetchOne();

        if ($property_analysis) {
            return $this->updatePropertyAnalysis($data);
        } else {
            $incr = $this->db->insert('property_analysis')
                ->value('property_id', $property_id)
                ->value('vote_count', 1)
                ->execute();
        }

        if ($incr) {
            return $this->packOk();
        }
        return $this->packError('property_analysis', 'incr-failed');
    }

    public function updatePropertyAnalysis($data = [])
    {
        $property_id = prop($data, 'property_id', 0);
        $op = prop($data, 'op', 1);

        $update = $this->db->update('property_analysis')
            ->incr('vote_count', $op)
            ->where('property_id', '=', $property_id, 'int')
            ->execute();
        if ($update) {
            return $this->packOk();
        }
        return $this->packError('property_analysis', 'updaye-failed');
    }

    public function findPropertyVote($query = [])
    {
        $property_id = prop($query, 'property_id', 0);
        $uid = prop($query, 'uid', current_uid());
        return $this->db->select()
            ->from('property_vote')
            ->where('property_id', '=', $property_id, 'int')
            ->andWhere('uid', '=', $uid, 'int')
            ->fetchOne();

    }

    public function findPropertyDstEid($property_id)
    {
        $find = $this->db->select(['dst_eid'])
            ->from('property')
            ->where('id', '=', $property_id, 'int')
            ->fetchOne();
        return $find ? $find->dst_eid : 0;
    }

    public function schVoteUserSsb($query = [])
    {
        $property_id = prop($query, 'property_id', 0);
        $uid = prop($query, 'uid', 0);
        $ssb = $this->db->select()
            ->from('property_vote')
            ->setDto('property_vote');
        if ($property_id) {
            $ssb->andWhere('property_id', '=', $property_id, 'int');
        }
        if ($uid) {
            $ssb->andWhere('uid', '=', $uid, 'int');
        }
        return $ssb;
    }

    public function schVoteUserSet($query = [])
    {
        return $this->DataSet(
            $this->schVoteUserSsb($query)
        );
    }

    public function countVoteUser($pid)
    {
        return $this->schVoteUserSet(['property_id' => $pid])->getItemCount();
    }
}
