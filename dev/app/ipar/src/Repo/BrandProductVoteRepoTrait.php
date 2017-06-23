<?php
namespace Ipar\Repo;

trait BrandProductVoteRepoTrait
{
    public function voteBrandProduct($brand_product_id)
    {
        if (!$this->findBrandProduct(['brand_product_id' => $brand_product_id])) {
            return $this->packError('brand_product_id', 'not-found');
        }

        if ($this->hasVoted($brand_product_id)) {
            return $this->packError('vote', 'duplicated');
        }

        $this->db->beginTransaction();

        if (!$this->db->insert('brand_product_vote')
            ->value('brand_product_id', $brand_product_id, 'int')
            ->value('vote_uid', $this->getCurrentUid())
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('brand_product_vote', 'insert-failed');
        }

        if (!$this->incrVoteCount($brand_product_id)) {
            $this->db->rollback();
            return $this->packError('brand_product', 'incr-vote_count-failed');
        }

        $this->db->commit();
        return $this->packItem('brand_product_id', $brand_product_id);
    }

    public function unvoteBrandProduct($brand_product_id)
    {
        if (!$this->findBrandProduct(['brand_product_id' => $brand_product_id])) {
            return $this->packError('brand_product_id', 'not-found');
        }

        if (!$this->hasVoted($brand_product_id)) {
            return $this->packError('vote', 'not-voted');
        }

        $this->db->beginTransaction();

        if (!$this->db->delete()
            ->from('brand_product_vote')
            ->where('brand_product_id', '=', $brand_product_id, 'int')
            ->andWhere('vote_uid', '=', $this->getCurrentUid(), 'int')
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('brand_product_vote', 'delete-failed');
        }

        if (!$this->incrVoteCount($brand_product_id, -1)) {
            $this->db->rollback();
            return $this->packError('brand_product', 'incr-vote_count-failed');
        }

        $this->db->commit();
        return $this->packItem('brand_product_id', $brand_product_id);
    }

    public function hasVoted($brand_product_id)
    {
        if ($this->findBrandProductVote([
            'brand_product_id' => $brand_product_id,
            'vote_uid' => $this->getCurrentUid()
        ])) {
            return true;
        }

        return false;
    }

    public function schBrandProductVoteSsb($query = [])
    {
        $ssb = $this->db->select()
            ->from('brand_product_vote')
            ->setDto('brand_product_vote');

        if ($brand_product_id = (int) prop($query, 'brand_product_id')) {
            $ssb->andWhere('brand_product_id', '=', $brand_product_id, 'int');
        }
        if ($vote_uid = prop($query, 'vote_uid')) {
            $ssb->andWhere('vote_uid', '=', $vote_uid, 'int');
        }
        return $ssb;
    }

    public function findBrandProductVote($query = [])
    {
        $ssb = $this->schBrandProductVoteSsb($query);
        if (!$ssb->getWheres()) {
            return null;
        }
        return $ssb->fetchOne();
    }

    public function incrVoteCount($brand_product_id, $rate = 1)
    {
        return $this->db->update('brand_product')
            ->incr('vote_count', $rate)
            ->where('id', '=', $brand_product_id, 'int')
            ->execute();
    }
}
