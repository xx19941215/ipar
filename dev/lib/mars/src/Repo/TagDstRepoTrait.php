<?php
namespace Mars\Repo;

trait TagDstRepoTrait
{
    public function saveTagDst($dst_type, $dst_id, $title)
    {
        $this->db->beginTransaction();

        $pack = $this->saveTag($title);
        if (!$pack->isOk()) {
            $this->db->rollback();
            return $pack;
        }

        $tag_id = $pack->getItem('tag_id');

        $tag_dst_id = $this->findTagDstId([
            'dst_type' => $dst_type,
            'dst_id' => $dst_id,
            'tag_id' => $tag_id
        ]);

        if (!$tag_dst_id) {
            $pack = $this->createTagDst($dst_type, $dst_id, $tag_id);
            if (!$pack->isOk()) {
                $this->db->rollback();
                return $pack;
            }
            $tag_dst_id = $pack->getItem('tag_dst_id');
        }

        $pack = $this->voteTagDst($tag_dst_id);
        if (!$pack->isOk()) {
            $this->db->rollback();
            return $pack;
        }

        $this->db->commit();
        return $this->packItem('tag_dst_id', $tag_dst_id);
    }

    public function createTagDst($dst_type, $dst_id, $tag_id)
    {
        $created = $this->db->insert('tag_dst')
            ->value('dst_type', $dst_type, 'int')
            ->value('dst_id', $dst_id, 'int')
            ->value('tag_id', $tag_id, 'int')
            ->execute();
        if ($created) {
            return $this->packItem(
                'tag_dst_id',
                $this->db->lastInsertId()
            );
        }

        return $this->packError('tag_dst', 'insert-failed');
    }

    public function deleteTagDst($query = [])
    {
        $dsb = $this->db->delete('td', 'tdv')
            ->from(['tag_dst', 'td'])
            ->leftJoin(
                ['tag_dst_vote', 'tdv'],
                ['tdv', 'tag_dst_id'],
                '=',
                ['td', 'id']
            );
        if ($tag_dst_id = (int) prop($query, 'tag_dst_id')) {
            $dsb->andWhere(['td', 'id'], '=', $tag_dst_id, 'int');
        }

        if ($dst_type = (int) prop($query, 'dst_type')) {
            $dsb->andWhere(['td', 'dst_type'], '=', $dst_type, 'int');
        }

        if ($dst_id = (int) prop($query, 'dst_id')) {
            $dsb->andWhere(['td', 'dst_id'], '=', $dst_id, 'int');
        }

        if (!$dsb->getWheres()) {
            return $this->packError('query', 'error-query');
        }

        if (!$dsb->execute()) {
            return $this->packError('tag_dst', 'delete-failed');
        }

        return $this->packOk();
    }

    public function schTagDstSsb($query = [])
    {
        $ssb = $this->db->select()
            ->setDto('tag_dst')
            ->from('tag_dst');
        if ($dst_type = prop($query, 'dst_type')) {
            $ssb->andWhere('dst_type', '=', $dst_type, 'int');
        }
        if ($dst_id = prop($query, 'dst_id')) {
            $ssb->andWhere('dst_id', '=', $dst_id, 'int');
        }
        if ($tag_id = prop($query, 'tag_id')) {
            $ssb->andWhere('tag_id', '=', $tag_id, 'int');
        }
        if ($tag_dst_id = prop($query, 'tag_dst_id')) {
            $ssb->andWhere('id', '=', $tag_dst_id, 'int');
        }

        $ssb->orderBy('vote_count', 'DESC');
        $ssb->orderBy('id', 'DESC');

        return $ssb;
    }

    public function findTagDst($query = [])
    {
        $ssb = $this->schTagDstSsb($query);
        if (!$ssb->getwheres()) {
            // todo
            return null;
        }
        return $ssb->fetchone();
    }

    public function findTagDstId($query = [])
    {
        if ($tag_dst = $this->findTagDst($query)) {
            return $tag_dst->id;
        }

        return 0;
    }
}
