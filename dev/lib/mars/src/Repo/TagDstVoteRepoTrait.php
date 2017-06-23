<?php
namespace Mars\Repo;

trait TagDstVoteRepoTrait
{
    public function voteTagDst($tag_dst_id)
    {
        if (!$this->findTagDst(['tag_dst_id' => $tag_dst_id])) {
            return $this->packError('tag_dst_id', 'not-found');
        }

        if ($this->hasVoted($tag_dst_id)) {
            return $this->packError('vote', 'duplicated');
        }

        $this->db->beginTransaction();
        if (!$this->db->insert('tag_dst_vote')
            ->value('tag_dst_id', $tag_dst_id, 'int')
            ->value('vote_uid', current_uid())
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('tag_dst_vote', 'insert-failed');
        }

        if (!$this->incrVoteCount($tag_dst_id)) {
            $this->db->rollback();
            return $this->packError('tag_dst', 'incr-vote_count-failed');
        }

        $this->db->commit();
        return $this->packItem('tag_dst_id', $tag_dst_id);
    }

    public function unvoteTagDst($tag_dst_id)
    {
        if (!$this->findTagDst(['tag_dst_id' => $tag_dst_id])) {
            return $this->packError('tag_dst_id', 'not-found');
        }

        if (!$this->hasVoted($tag_dst_id)) {
            return $this->packError('vote', 'not-voted');
        }

        $this->db->beginTransaction();

        if (!$this->db->delete()
            ->from('tag_dst_vote')
            ->where('tag_dst_id', '=', $tag_dst_id, 'int')
            ->andWhere('vote_uid', '=', current_uid())
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('tag_dst_vote', 'delete-failed');
        }

        if (!$this->incrVoteCount($tag_dst_id, -1)) {
            $this->db->rollback();
            return $this->packError('tag_dst', 'incr-vote_count-failed');
        }

        $this->db->commit();
        return $this->packItem('tag_dst_id', $tag_dst_id);
    }

    public function hasVoted($tag_dst_id)
    {
        $tag_dst_vote = $this->findTagDstVote([
            'tag_dst_id' => $tag_dst_id,
            'vote_uid' => current_uid()
        ]);

        if ($tag_dst_vote) {
            return true;
        }

        return false;
    }

    public function schTagDstVoteSsb($query = [])
    {
        $ssb = $this->db->select()
            ->from('tag_dst_vote')
            ->setDto('tag_dst_vote');
        if ($tag_dst_id = prop($query, 'tag_dst_id')) {
            $ssb->andWhere('tag_dst_id', '=', $tag_dst_id, 'int');
        }
        if ($vote_uid = prop($query, 'vote_uid')) {
            $ssb->andWhere('vote_uid', '=', $vote_uid, 'int');
        }
        return $ssb;
    }

    public function findTagDstVote($query = [])
    {
        $ssb = $this->schTagDstVoteSsb($query);
        if (!$ssb->getWheres()) {
            return null;
        }
        return $ssb->fetchOne();
    }

    public function incrVoteCount($tag_dst_id, $rate = 1)
    {
        return $this->db->update('tag_dst')
            ->incr('vote_count', $rate)
            ->where('id', '=', $tag_dst_id, 'int')
            ->execute();
    }
}
