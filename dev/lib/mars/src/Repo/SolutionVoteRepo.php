<?php

namespace Mars\Repo;

class SolutionVoteRepo extends MarsRepoBase
{
    public function vote($solution_id)
    {
        $this->db->beginTransaction();

        $create = $this->createSolution(['solution_id' => $solution_id]);
        if (!$create->isOk()) {
            $this->db->rollback();
            return $create;
        }

        $incr = $this->incrSolutionAnalysis(['solution_id' => $solution_id]);
        if (!$incr->isOk()) {
            $this->db->rollback();
            return $incr;
        }

        $this->db->commit();
        return $this->packItem('dst_eid', $this->findSolutionDstEid($solution_id));
    }

    public function createSolution($data = [])
    {
        $solution_id = prop($data, 'solution_id', 0);
        $uid = prop($data, 'uid', current_uid());
        if ($solution_id <= 0) {
            return $this->packError('solution_id', 'not-positive');
        }
        if ($this->findSolutionVote(['solution_id' => $solution_id])) {
            return $this->packError('solution_vote', 'had-exist');
        }
        $create = $this->db->insert('solution_vote')
            ->value('solution_id', $solution_id)
            ->value('uid', $uid)
            ->value('status', 1)
            ->execute();

        if ($create) {
            return $this->packOk();
        }
        return $this->packError('solution', 'insert-failed');
    }

    public function unvote($solution_id)
    {
        $this->db->beginTransaction();

        $delete = $this->deleteSolution(['solution_id' => $solution_id]);
        if (!$delete->isOk()) {
            $this->db->rollback();
            return $delete;
        }

        $this->db->commit();
        return $this->packItem('dst_eid', $this->findSolutionDstEid($solution_id));
    }

    public function deleteSolution($query = [])
    {
        $solution_id = prop($query, 'solution_id', 0);
        $uid = prop($query, 'uid', current_uid());
        if ($solution_id <= 0) {
            return $this->packError('solution_id', 'not-positive');
        }

        if (!$this->findSolutionVote(['solution_id' => $solution_id])) {
            return $this->packError('solution_vote', 'not-found');
        }

        $delete = $this->db->delete()
            ->from('solution_vote')
            ->where('solution_id', '=', $solution_id, 'int')
            ->andWhere('uid', '=', $uid, 'int')
            ->execute();

        if ($delete) {
            return $this->packOk();
        }
        return $this->packError('solution', 'insert-failed');
    }

    public function incrSolutionAnalysis($data = [])
    {
        $solution_id = prop($data, 'solution_id', 0);

        $solution_analysis = $this->db->select()
            ->from('solution_analysis')
            ->where('solution_id', '=', $solution_id, 'int')
            ->fetchOne();

        if ($solution_analysis) {
            return $this->updateSolutionAnalysis($data);
        } else {
            $incr = $this->db->insert('solution_analysis')
                ->value('solution_id', $solution_id)
                ->value('vote_count', 1)
                ->execute();
        }

        if ($incr) {
            return $this->packOk();
        }
        return $this->packError('solution_analysis', 'incr-failed');
    }

    public function updateSolutionAnalysis($data = [])
    {
        $solution_id = prop($data, 'solution_id', 0);
        $op = prop($data, 'op', 1);

        $update = $this->db->update('solution_analysis')
            ->incr('vote_count', $op)
            ->where('solution_id', '=', $solution_id, 'int')
            ->execute();
        if ($update) {
            return $this->packOk();
        }
        return $this->packError('solution_analysis', 'updaye-failed');
    }

    public function findSolutionVote($query = [])
    {
        $solution_id = prop($query, 'solution_id', 0);
        $uid = prop($query, 'uid', current_uid());
        return $this->db->select()
            ->from('solution_vote')
            ->where('solution_id', '=', $solution_id, 'int')
            ->andWhere('uid', '=', $uid, 'int')
            ->fetchOne();
    }

    public function findSolutionDstEid($solution_id)
    {
        $find = $this->db->select(['dst_eid'])
            ->from('solution')
            ->where('id', '=', $solution_id, 'int')
            ->fetchOne();
        return $find ? $find->dst_eid : 0;
    }

    public function schVoteUserSsb($query = [])
    {
        $solution_id = prop($query, 'sid', 0);
        $uid = prop($query, 'uid', 0);
        $ssb = $this->db->select()
            ->from('solution_vote')
            ->setDto('solution_vote');
        if ($solution_id) {
            $ssb->andWhere('solution_id', '=', $solution_id, 'int');
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

    public function countVoteUser($sid)
    {
        return $this->schVoteUserSet(['sid' => $sid])->getItemCount();
    }

}
