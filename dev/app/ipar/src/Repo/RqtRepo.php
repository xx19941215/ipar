<?php

namespace Ipar\Repo;

class RqtRepo extends \Mars\Repo\EntityRepo
{
    public function getRqtByEid($eid)
    {
        return $this->getEntityByEid($eid);
    }

    public function getRqtByZcode($zcode)
    {
        return $this->getEntityByZcode($zcode);
    }

    public function createRqt($title, $content = '')
    {
        $this->db->beginTransaction();

        $this->createEntity('rqt', ['title' => $title, 'content' => $content]);

        $rqt_eid = $this->lastEid();
        $this->createStory('create', 'rqt', $rqt_eid, $this->lastSubmitId());

        $this->db->commit();
        return $this->packItem('eid', $rqt_eid);
    }

    public function updateRqt($eid, $title, $content = '')
    {
        $this->db->beginTransaction();

        $this->updateEntity($eid, ['title' => $title, 'content' => $content]);

        $this->createStory('update', 'rqt', $eid, $this->lastSubmitId());

        $this->db->commit();
        return $this->packOk();
    }

    public function deleteRqt($eid)
    {
        return $this->deleteRqtByEid($eid);
    }
    public function deleteRqtByEid($eid)
    {
        $this->db->beginTransaction();

        try {
            $this->deleteEntity($eid);
            $this->db->delete()->from('solution')->where('eid', '=', $eid, 'int');
            $this->db->delete()->from('solution')->where('dst_eid', '=', $eid, 'int');
        } catch (\Exception $e) {
            $this->db->rollback();
            return $this->packError('exception', $e->getMessage());
        }

        $this->db->commit();
        return $this->packOk();
    }

    //
    // count
    //
    public function countSolution($rqt_eid)
    {
        $count = $this->db->select()
            ->from('solution')
            ->where('rqt_eid', '=', $rqt_eid, 'int')
            ->count();
        $this->_rqt_analysis($rqt_eid);
        $this->db->update('rqt_analysis')->where('rqt_eid', '=', $rqt_eid, 'int')->set('solution_count', $count, 'int')->execute();
        return $count;
    }

    public function countSidea($rqt_eid)
    {
        $count = $this->db->select()
            ->from('solution')
            ->where('rqt_eid', '=', $rqt_eid, 'int')
            ->andWhere('stype_id', '=', get_type_id('idea'), 'int')
            ->count();
        $this->_rqt_analysis($rqt_eid);
        $this->db->update('rqt_analysis')->where('rqt_eid', '=', $rqt_eid, 'int')->set('idea_count', $count, 'int')->execute();
        return $count;
    }

    public function countSproduct($rqt_eid)
    {
        $count = $this->db->select()
            ->from('solution')
            ->where('rqt_eid', '=', $rqt_eid, 'int')
            ->andWhere('stype_id', '=', get_type_id('product'), 'int')
            ->count();
        $this->_rqt_analysis($rqt_eid);
        $this->db->update('rqt_analysis')->where('rqt_eid', '=', $rqt_eid, 'int')->set('product_count', $count, 'int')->execute();
        return $count;
    }

    public function countSinvent($rqt_eid)
    {
        $count = $this->db->select()
            ->from('solution')
            ->where('rqt_eid', '=', $rqt_eid, 'int')
            ->andWhere('stype_id', '=', get_type_id('invent'), 'int')
            ->count();
        $this->_rqt_analysis($rqt_eid);
        $this->db->update('rqt_analysis')->where('rqt_eid', '=', $rqt_eid, 'int')->set('invent_count', $count, 'int')->execute();
        return $count;
    }

    /**
     * create solution
     */

    public function createSolution($rqt_eid, $type_key, $dst_eid)
    {
        $rqt_eid = (int) $rqt_eid;
        $dst_eid = (int) $dst_eid;

        if ($rqt_eid <= 0) {
            return $this->packError('rqt_eid', 'not-positive');
        }
        if ($dst_eid <= 0) {
            return $this->packError('dst_eid', 'not-positive');
        }

        $this->db->beginTransaction();

        $this->_createSolution($rqt_eid, $type_key, $dst_eid);

        $dst_entity = $this->getEntityByEid($dst_eid);
        $this->createStory('recommend', $type_key, $dst_eid, $dst_entity->getSubmitId(), $rqt_eid);

        $this->db->commit();
        return $this->packItem("{$type_key}_eid", $dst_eid);
    }

    public function createSidea($rqt_eid, $content)
    {
        $rqt_eid = (int) $rqt_eid;
        if ($rqt_eid <= 0) {
            return $this->packError('rqt_eid', 'not-positive');
        }

        $content = trim($content);
        $this->db->beginTransaction();

        $this->createEntity('idea', ['content' => $content]);

        $idea_eid = $this->lastEid();
        $this->createStory('create', 'idea', $idea_eid, $this->lastSubmitId(), $rqt_eid);
        $this->_createSolution($rqt_eid, 'idea', $idea_eid);

        $this->db->commit();
        return $this->packItem('idea_eid', $idea_eid);
    }

    public function createSproduct($rqt_eid, $title, $content, $url, $published = '')
    {
        if ($rqt_eid <= 0) {
            return $this->packError('rqt_eid', 'not-positive');
        }

        $this->db->beginTransaction();
        $this->createEntity('product', ['title' => $title, 'content' => $content, 'url' => $url]);

        $product_eid = $this->lastEid();
        $this->createStory('create', 'product', $product_eid, $this->lastSubmitId(), $rqt_eid);
        $this->_createSolution($rqt_eid, 'product', $product_eid);
        $this->_createProperty($product_eid, 'solved', $rqt_eid);

        if (!$this->db->insert('product')
            ->value('eid', $product_eid)
            ->value('url', $url)
            ->value('published', $published ? $published : '0000-00-00 00:00:00')
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('product', 'insert-failed');
        }

        $this->db->commit();
        return $this->packItem('product_eid', $product_eid);
    }

    /**
     * search solution
     */
    public function schSolutionBd($rqt_eid, $opts = [])
    {
        $query = prop($opts, 'query', '');
        $stype_key = prop($opts, 'stype_key', '');
        $status = prop($opts, 'status', 1);

        $builder = $this->db->select(
                ['s', 'id'],
                ['s', 'rqt_eid'],
                ['s', 'stype_id'],
                ['s', 'dst_eid'],
                ['e', 'eid'],
                ['e', 'zcode'],
                ['e', 'title'],
                ['e', 'content'],
                ['e', 'imgs'],
                ['e', 'created'],
                ['e', 'changed'],
                ['e', 'status'],
                ['e', 'type_id']
            )
            ->from(['solution', 's'])
            ->leftJoin(
                ['entity', 'e'],
                ['e', 'eid'],
                '=',
                ['s', 'dst_eid']
            )
            ->where(['s', 'rqt_eid'], '=', $rqt_eid, 'int');

        if ($query) {
            $match_against = 'MATCH(`e`.`title`, `e`.`content`) against(:query IN BOOLEAN MODE)';
            $builder->andWhereRaw($match_against)
                ->bindValue(':query', $query)
                ->orderByRaw($match_against . ' DESC');
        }
        if ($stype_key) {
            $builder->andWhere(['s', 'stype_id'], '=', get_type_id($stype_key), 'int');
            $builder->setDto($stype_key);
        } else {
            $builder->setDto('entity');
        }
        if ($status !== null) {
            $builder->andWhere(['e', 'status'], '=', $status, 'int');
        }
        $builder->orderBy(['s', 'changed'], 'DESC');
        return $builder;
    }

    protected function _rqt_analysis($rqt_eid)
    {
        if (!$this->db->select('rqt_eid')
            ->from('rqt_analysis')
            ->where('rqt_eid', '=', $rqt_eid, 'int')
            ->fetchOne()
        ) {
            $this->db->insert('rqt_analysis')->value('rqt_eid', $rqt_eid)->execute();
        }
    }

    public function schRqtSet($opts = [])
    {
        return $this->dataSet(
            $this->schEntitySsb($opts)
        );
    }

    public function schSolutionSet($rqt_eid, $opts = [])
    {
        return $this->dataSet(
            $this->schSolutionBd($rqt_eid, $opts)
        );
    }

    public function getRqtSet($opts)
    {
        $ssb =
            $this->db->select()
            ->setDto('rqt')
            ->from('entity')
            ->where('type_id', '=', '1');

        $order_by = ($opts['sort'] == 0) ? 'rank' : 'created';
        $ssb->orderBy($order_by, 'DESC');
        return $this->dataSet($ssb);
    }
}
