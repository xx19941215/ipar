<?php

namespace Ipar\Repo;

class ProductRepo extends \Mars\Repo\EntityRepo
{

    public function getProductByEid($eid)
    {
        $arr = $builder = $this->db->select(
            ['e', 'eid'],
            ['e', 'type_id'],
            ['e', 'zcode'],
            ['e', 'owner_uid'],
            ['e', 'uid'],
            ['e', 'title'],
            ['e', 'content'],
            ['e', 'imgs'],
            ['e', 'rank'],
            ['e', 'status'],
            ['e', 'created'],
            ['e', 'changed'],
            ['p', 'url'],
            ['p', 'published']
        )
            ->from(['entity', 'e'])
            ->leftJoin(
                ['product', 'p'],
                ['p', 'eid'],
                '=',
                ['e', 'eid']
            )
            ->setFetchAssoc()
            //->where(['e', 'status'], '=', 1, 'int')
            ->andWhere(['e', 'eid'], '=', $eid, 'int')
            ->fetchOne();

        return arr2dto($arr);
    }

    public function getProductByZcode($zcode)
    {
        return $this->getEntityByZcode($zcode);
    }

    public function createProduct($title, $content, $url = '', $published = '')
    {
        $url = (string)$url;

        $title = trim($title);
        $title = preg_replace('!\s+!', ' ', $title);
        $this->db->beginTransaction();

        $this->createEntity('product', [
            'title' => $title,
            'content' => $content,
            'url' => $url,
            'published' => $published
        ]);

        $product_eid = $this->lastEid();
        $this->createStory('create', 'product', $product_eid, $this->lastSubmitId());

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
        return $this->packItem('eid', $product_eid);

    }

    public function updateProduct($eid, $title, $content, $url = '', $published = '')
    {
        $url = (string)$url;
        $this->db->beginTransaction();

        $this->updateEntity($eid, [
            'title' => $title,
            'content' => $content,
            'url' => $url,
            'published' => $published
        ]);

        $this->createStory('update', 'product', $eid, $this->lastSubmitId());

        if (!$this->db->update('product')
            ->where('eid', '=', $eid, 'int')
            ->set('url', $url)
            ->set('published', $published ? $published : '0000-00-00 00:00:00')
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('product', 'update-failed');
        }

        $this->db->commit();
        return $this->packOk();
    }

    public function deleteProductByEid($eid)
    {
        $this->db->beginTransaction();

        $this->deleteEntity($eid);
        if (!$this->db->delete()->from('solution')->where('dst_eid', '=', $eid, 'int')) {
            $this->db->rollback();
            return $this->packError('solution', 'delete-failed');
        }
        if (!$this->db->delete()->from('propery')->where('product_eid', '=', $eid, 'int')) {
            $this->db->rollback();
            return $this->packError('property', 'delete-failed');
        }

        $this->db->commit();
        return $this->packOk();

    }

    public function schProductSsb($opts = [])
    {
        $opts['type_key'] = 'product';
        return $this->schEntitySsb($opts);
    }

    public function getProductSsb($opts = [])
    {
        $opts['type_key'] = 'product';
        return $this->getEntitySsb($opts);
    }

    /*
     * create property
     */
    public function createProperty($product_eid, $ptype_key, $dst_eid, $extra = [])
    {
        $this->db->beginTransaction();
        $this->_createProperty($product_eid, $ptype_key, $dst_eid, $extra);

        $dst_entity = $this->getEntityByEid($dst_eid);
        $this->createStory('recommend', $ptype_key, $dst_eid, $dst_entity->getSubmitId(), $product_eid);

        if ($ptype_key == 'solved') {
            $type_key = 'rqt';
        } else if ($ptype_key == 'improving') {
            $type_key = 'rqt';
        } else {
            $type_key = $ptype_key;
        }

        $this->db->commit();
        return $this->packItem("{$type_key}_eid", $dst_eid);
    }

    public function createPsolved($product_eid, $title, $content)
    {
        $this->db->beginTransaction();
        $this->createEntity('rqt', ['title' => $title, 'content' => $content]);

        $rqt_eid = $this->lastEid();
        $this->createStory('create', 'solved', $rqt_eid, $this->lastSubmitId(), $product_eid);
        $this->_createProperty($product_eid, 'solved', $rqt_eid);
        $this->_createSolution($rqt_eid, 'product', $product_eid);

        $this->db->commit();
        return $this->packItem('rqt_eid', $rqt_eid);
    }

    public function createPimproving($product_eid, $title, $content)
    {
        $this->db->beginTransaction();
        $this->createEntity('rqt', ['title' => $title, 'content' => $content]);

        $rqt_eid = $this->lastEid();
        $this->createStory('create', 'improving', $rqt_eid, $this->lastSubmitId(), $product_eid);
        $this->_createProperty($product_eid, 'improving', $rqt_eid);
        $this->_createSolution($rqt_eid, 'product', $product_eid);

        $this->db->commit();
        return $this->packItem('rqt_eid', $rqt_eid);
    }

    public function createPfeature($product_eid, $title, $content)
    {
        $this->db->beginTransaction();
        $this->createEntity('feature', ['title' => $title, 'content' => $content]);

        $feature_eid = $this->lastEid();
        $this->createStory('create', 'feature', $feature_eid, $this->lastSubmitId(), $product_eid);
        $this->_createProperty($product_eid, 'feature', $feature_eid);

        $this->db->commit();
        return $this->packItem('feature_eid', $feature_eid);
    }

    public function createPbranch($product_eid, $title)
    {
        if ($this->db->insert('p_branch')
            ->value('product_eid', $product_eid, 'int')
            ->value('title', $title)
            ->execute()
        ) {
            return $this->packOk();
        } else {
            return $this->packError('p_branch', 'insert-failed');
        }
    }

    public function createPtag($product_eid, $title)
    {
        if ($this->db->insert('p_tag')
            ->value('product_eid', $product_eid, 'int')
            ->value('title', $title)
            ->execute()
        ) {
            return $this->packOk();
        } else {
            return $this->packError('p_tag', 'insert-failed');
        }
    }

    public function createPtarget($product_eid, $title)
    {
        if ($this->db->insert('p_target')
            ->value('product_eid', $product_eid, 'int')
            ->value('title', $title)
            ->execute()
        ) {
            return $this->packOk();
        } else {
            return $this->packError('p_target', 'insert-failed');
        }
    }

    //
    // count
    //
    public function countProperty($product_eid)
    {
        $count = $this->db->select()
            ->from('property')
            ->where('product_eid', '=', $product_eid, 'int')
            ->count();
        $this->_product_analysis($product_eid);
        $this->db->update('product_analysis')->where('product_eid', '=', $product_eid, 'int')->set('property_count', $count, 'int')->execute();
        return $count;
    }

    public function countPsolved($product_eid)
    {
        $count = $this->db->select()
            ->from(['property', 'p'])
            ->innerJoin(['entity', 'e'], ['p', 'dst_eid'], '=', ['e', 'eid'])
            ->where('product_eid', '=', $product_eid, 'int')
            ->andWhere('ptype_id', '=', get_type_id('solved'), 'int')
            ->andWhere(['e', 'status'], '=', 1)
            ->limit(0)
            ->count();
        $this->_product_analysis($product_eid);
        $this->db->update('product_analysis')->where('product_eid', '=', $product_eid, 'int')->set('solved_count', $count, 'int')->execute();
        return $count;
    }

    public function countPimproving($product_eid)
    {
        $count = $this->db->select()
            ->from(['property', 'p'])
            ->innerJoin(['entity', 'e'], ['p', 'dst_eid'], '=', ['e', 'eid'])
            ->where(['p', 'product_eid'], '=', $product_eid, 'int')
            ->andWhere(['p', 'ptype_id'], '=', get_type_id('improving'), 'int')
            ->andWhere(['e', 'status'], '=', 1)
            ->limit(0)
            ->count();
        $this->_product_analysis($product_eid);
        $this->db->update('product_analysis')->where('product_eid', '=', $product_eid, 'int')->set('improving_count', $count, 'int')->execute();
        return $count;
    }

    public function countPfeature($product_eid)
    {
        $ptype_eid = get_type_id('feature');
        $ssb = $this->db->select()
            ->from(['property', 'p'])
            ->leftJoin(['entity', 'e'], ['e', 'eid'], '=', ['p', 'dst_eid'])
            ->where(['p', 'product_eid'], '=', $product_eid, 'int')
            ->andWhere(['p', 'ptype_id'], '=', $ptype_eid, 'int')
            ->andWhere(['e', 'status'], '=', 1, 'int');

        $count = $ssb->count();
        $this->_product_analysis($product_eid);
        $this->db->update('product_analysis')->where('product_eid', '=', $product_eid, 'int')->set('feature_count', $count, 'int')->execute();
        return $count ? $count : 0;
    }

    /**
     * search property
     */
    public function schPropertySsb($product_eid, $opts = [])
    {
        $query = prop($opts, 'query', '');
        $ptype_key = prop($opts, 'ptype_key', '');
        $status = prop($opts, 'status', 1);

        $ssb = $this->db->select(
            ['p', 'id'],
            ['p', 'product_eid'],
            ['p', 'ptype_id'],
            ['p', 'dst_eid'],
            ['e', 'eid'],
            ['e', 'zcode'],
            ['e', 'title'],
            ['e', 'content'],
            ['e', 'imgs'],
            ['e', 'created'],
            ['e', 'changed'],
            ['e', 'status'],
            ['e', 'type_id'],
            ['e','uid']
        )
            ->from(['property', 'p'])
            ->leftJoin(
                ['entity', 'e'],
                ['e', 'eid'],
                '=',
                ['p', 'dst_eid']
            )
            ->where(['p', 'product_eid'], '=', $product_eid, 'int');

        if ($query) {
            $match_against = 'MATCH(`e`.`title`, `e`.`content`) against(:query IN BOOLEAN MODE)';
            $ssb->andWhereRaw($match_against)
                ->bindValue(':query', $query)
                ->orderByRaw($match_against . ' DESC');
        }

        if ($ptype_key) {
            if ($ptype_key == 'solved') {
                $type_key = 'rqt';
            } else if ($ptype_key == 'improving') {
                $type_key = 'rqt';
            } else {
                $type_key = $ptype_key;
            }
            $ssb->andWhere(['p', 'ptype_id'], '=', get_type_id($ptype_key), 'int');
            $ssb->setDto($type_key);
        } else {
            $ssb->setDto('entity');
        }

        if ($status !== null) {
            $ssb->andWhere(['e', 'status'], '=', $status, 'int');
        }

        $ssb->orderBy(['p', 'changed'], 'DESC');
        return $ssb;

    }

    public function schPbranchSsb($product_eid)
    {
        $ssb = $this->db->select()
            ->from('p_branch')
            ->where('product_eid', '=', $product_eid, 'int');
        return $ssb;
    }

    public function schPtagSsb($product_eid)
    {
        $ssb = $this->db->select()
            ->from('p_tag')
            ->where('product_eid', '=', $product_eid, 'int');
        return $ssb;
    }

    public function schPtargetSsb($product_eid)
    {
        $ssb = $this->db->select()
            ->from('p_target')
            ->where('product_eid', '=', $product_eid, 'int');
        return $ssb;
    }

    public function updatePbranch($pbranch_id, $title)
    {
        $updated = $this->db->update('p_branch')
            ->where('id', '=', $pbranch_id, 'int')
            ->set('title', $title)
            ->execute();

        if ($updated) {
            return $this->packOk();
        } else {
            return $this->packError('p_branch', 'update-failed');
        }
    }

    public function updatePtag($ptag_id, $title)
    {
        $updated = $this->db->update('p_tag')
            ->where('id', '=', $ptag_id, 'int')
            ->set('title', $title)
            ->execute();

        if ($updated) {
            return $this->packOk();
        } else {
            return $this->packError('p_tag', 'update-failed');
        }
    }

    public function updatePtarget($ptarget_id, $title)
    {
        $updated = $this->db->update('p_target')
            ->where('id', '=', $ptarget_id, 'int')
            ->set('title', $title)
            ->execute();

        if ($updated) {
            return $this->packOk();
        } else {
            return $this->packError('p_target', 'update-failed');
        }
    }

    public function getPbranchById($pbranch_id)
    {
        return $this->db->select()
            ->from('p_branch')
            ->where('id', '=', $pbranch_id, 'int')
            ->fetchOne();
    }

    public function getPtagById($ptag_id)
    {
        return $this->db->select()
            ->from('p_tag')
            ->where('id', '=', $ptag_id, 'int')
            ->fetchOne();
    }

    public function getPtargetById($ptarget_id)
    {
        return $this->db->select()
            ->from('p_target')
            ->where('id', '=', $ptarget_id, 'int')
            ->fetchOne();
    }

    protected function _product_analysis($product_eid)
    {
        if (!$this->db->select('product_eid')
            ->from('product_analysis')
            ->where('product_eid', '=', $product_eid, 'int')
            ->fetchOne()
        ) {
            $this->db->insert('product_analysis')->value('product_eid', $product_eid)->execute();
        }
    }

    public function schProductSet($opts = [])
    {
        return $this->dataSet(
            $this->schEntitySsb($opts)
        );
    }

    public function schPropertySet($product_eid, $opts)
    {
        return $this->dataSet(
            $this->schPropertySsb($product_eid, $opts)
        );
    }

    public function schPbranchSet($product_eid)
    {
        return $this->dataSet(
            $this->schPbranchSsb($product_eid)
        );
    }

    public function getRqtHotTag()
    {
        $ssb = $this->db->select(['a', 'tag_id'], ['b', 'zcode'], ['b', 'title'])
            ->from(['rqt_tag_stat', 'a'])
            ->innerJoin(['tag', 'b'], ['a', 'tag_id'], '=', ['b', 'id'])
            ->orderBy(['a', 'count'], 'DESC')
            ->limit(7);
        return $ssb->fetchAll();
    }

    public function getProductHotTag()
    {
        $ssb = $this->db->select(['a', 'tag_id'], ['b', 'zcode'], ['b', 'title'])
            ->from(['product_tag_stat', 'a'])
            ->innerJoin(['tag', 'b'], ['a', 'tag_id'], '=', ['b', 'id'])
            ->orderBy(['a', 'count'], 'DESC')
            ->limit(7);
        return $ssb->fetchAll();
    }
}
