<?php

namespace Mars\Repo;

class EntityRepo extends MarsRepoBase
{

    protected $last_submit_id = 0;
    protected $last_eid = 0;

    //
    // public functions
    //

    public function schEntitySsb($opts = [])
    {
        $query = prop($opts, 'query', '');
        $type_key = prop($opts, 'type_key', '');
        $status = prop($opts, 'status', 1);
        $tag_id = prop($opts, 'tag_id');
        $brand_id = prop($opts, 'brand_id');
        $sort = prop($opts, 'sort');

        $builder = $this->prepareBd();

        if ($query) {
            $match_against = 'MATCH(`e`.`title`, `e`.`content`) against(:query IN BOOLEAN MODE)';
            $builder->startGroup()
                ->where(['e', 'title'], 'LIKE', "%{$query}%")
                ->orWhere(['e', 'content'], 'LIKE', "%{$query}%")
                ->endGroup();
          /*  $builder->andWhereRaw($match_against)
                ->bindValue(':query', $query)
                ->orderByRaw($match_against . ' DESC');*/
        }

        $sort ?  '' : $builder = $builder->orderBy('rank' , 'DESC');

        if ($type_key) {
            $builder->andWhere(['e', 'type_id'], '=', get_type_id($type_key), 'int');
            $builder->setDto($type_key);
        } else {
            $builder->setDto('entity');
        }

        if ($uid = prop($opts, 'uid', 0)) {
            $builder->andWhere(['e', 'uid'], '=', $uid, 'int');
        }

        if ($status !== null) {
            $builder->andWhere(['e', 'status'], '=', $status, 'int');
        }
        if ($tag_id) {
            $builder->leftJoin(
                ['tag_dst', 'dst_td'],
                ['dst_td', 'dst_id'],
                '=',
                ['e', 'eid']
            );
            //$bd->andWhere(['src_td', 'tag_id'], '=', $tag_id, 'int')
            $builder->andWhere(['dst_td', 'tag_id'], '=', $tag_id, 'int');
            $builder->orderBy(['dst_td', 'vote_count'], 'DESC');
        }
        if ($brand_id) {
            $builder->leftJoin(
                ['brand_product', 'bp'],
                ['bp', 'product_eid'],
                '=',
                ['e', 'eid']
            );
            //$bd->andWhere(['src_td', 'brand_id'], '=', $brand_id, 'int')
            $builder->andWhere(['bp', 'brand_id'], '=', $brand_id, 'int')
                ->andWhere(['e', 'type_id'], '=', get_type_id('product'), 'int');
            $builder->orderBy(['bp', 'vote_count'], 'DESC');
        }

        $order = prop($opts, 'order', 'default');
        if ($order == 'default') {
            $builder->orderBy(['e', 'eid'], 'DESC');
//            $builder->orderBy(['ls', 'src_count'], 'DESC');
        } else if ($order == 'created') {
            $builder->orderBy(['e', 'eid'], 'DESC');
        } else {
            var_dump($order);
            _debug('unexpected entity order');
        }
        return $builder;
    }

    /*
    public function getEntityBd($opts = [])
    {
        $bd = $this->prepareBd();
        $bd->orderBy(['e', 'eid'], 'ASC');
        if ($type_key = prop($opts, 'type_key', '')) {
            $bd->andWhere(['e', 'type_id'], '=', get_type_id($type_key), 'int');
            $bd->setDto($type_key);
        } else {
            $bd->setDto('entity');
        }
        return $bd;
    }
     */

    public function getEntityByEid($eid)
    {
        $arr = $this->prepareBd()
            ->setFetchAssoc()
            ->andWhere(['e', 'eid'], '=', $eid, 'int')
            ->fetchOne();

        return arr2dto($arr);
    }

    public function getEntityByZcode($zcode)
    {
        $arr = $this->prepareBd()
            ->setFetchAssoc()
            ->setFetchAssoc()
            ->andWhere(['e', 'zcode'], '=', $zcode)
            ->fetchOne();

        return arr2dto($arr);
    }

    public function activateEntity($eid)
    {
        if (!$this->db->update('entity')
            ->where('eid', '=', $eid, 'int')
            ->set('status', 1, 'int')
            ->execute()
        ) {
            return $this->packError('entity', 'update-failed');
        }

        return $this->packOk();
    }

    public function deactivateEntity($eid)
    {
        if (!$this->db->update('entity')
            ->where('eid', '=', $eid, 'int')
            ->set('status', 0, 'int')
            ->execute()
        ) {
            return $this->packError('entity', 'update-failed');
        }

        return $this->packOk();
    }

    public function lastSubmitId()
    {
        return $this->last_submit_id;
    }

    public function lastEid()
    {
        return $this->last_eid;
    }

    public function getSubmitBdByEid($eid)
    {
        $eid = (int) $eid;
        return $this->db->select()
            ->from('e_submit')
            ->where('eid', '=', $eid, 'int')
            ->orderBy('created', 'DESC');
    }

    //
    // count
    //
    public function countLike($eid)
    {
        $eid = (int) $eid;
        if (!$eid) {
            return 0;
        }

        $count = $this->db->select()
            ->from('entity_like')
            ->where('eid', '=', $eid, 'int')
            ->count();
        $this->_entity_analysis($eid);
        $this->db->update('entity_analysis')->where('eid', '=', $eid, 'int')->set('like_count', $count, 'int')->execute();
        return $count;
    }

    public function countComment($eid)
    {
        $count = $this->db->select()
            ->from('e_comment')
            ->where('eid', '=', $eid, 'int')
            ->andWhere('status', '=', 1, 'int')
            ->count();
        $this->_entity_analysis($eid);
        $this->db->update('entity_analysis')->where('eid', '=', $eid, 'int')->set('comment_count', $count, 'int')->execute();
        return $count;
    }

    public function countTag($eid)
    {
        $count = $this->db->select()
            ->from('entity_tag')
            ->where('eid', '=', $eid, 'int')
            ->count();
        $this->_entity_analysis($eid);
        $this->db->update('entity_analysis')->where('eid', '=', $eid, 'int')->set('tag_count', $count, 'int')->execute();
        return $count;
    }

    public function countTagVote($eid)
    {
        $count = $this->db->select()
            ->from(['entity_tag_vote', 'v'])
            ->leftJoin(
                ['entity_tag', 't'],
                ['t', 'id'],
                '=',
                ['v', 'entity_tag_id']
            )
            ->where(['t', 'eid'], '=', $eid, 'int')
            ->count();
        $this->_entity_analysis($eid);
        $this->db->update('entity_analysis')->where('eid', '=', $eid, 'int')->set('tag_vote_count', $count, 'int')->execute();
        return $count;
    }

    public function countSubmit($eid)
    {
        $count = $this->db->select()
            ->from('e_submit')
            ->where('eid', '=', $eid, 'int')
            ->count();
        $this->_entity_analysis($eid);
        $this->db->update('entity_analysis')->where('eid', '=', $eid, 'int')->set('submit_count', $count, 'int')->execute();
        return $count;
    }

    public function countSrc($eid)
    {
        $count = $this->db->select()
            ->from('story')
            ->where('src_eid', '=', $eid, 'int')
            ->count();
        $this->_entity_analysis($eid);
        $this->db->update('entity_analysis')->where('eid', '=', $eid, 'int')->set('src_count', $count, 'int')->execute();
        return $count;
    }

    public function countStory($eid)
    {
        $eid = (int) $eid;
        if (!$eid) {
            return 0;
        }

        $count = $this->db->select()
            ->from(['story', 's'])
            ->leftJoin(
                ['e_submit', 'mt'],
                ['mt', 'id'],
                '=',
                ['s', 'e_submit_id']
            )
            ->where(['s', 'src_eid'], '=', $eid, 'int')
            ->orWhere(['mt', 'eid'], '=', $eid, 'int')
            ->count();
        $this->_entity_analysis($eid);
        $this->db->update('entity_analysis')->where('eid', '=', $eid, 'int')->set('story_count', $count, 'int')->execute();
        return $count;
    }

    public function getLatestSubmitId($eid)
    {
        $submit = $this->db->select('id')
            ->from('e_submit')
            ->where('eid', '=', $eid, 'int')
            ->orderBy('id', 'DESC')
            ->fetchOne();

        if ($submit) {
            return $submit->id;
        }

        return 0;
    }
    //
    // protected functions
    //

    protected function prepareBd()
    {
        return $this->db->select(
                ['e', 'eid'],
                ['e', 'uid'],
                ['e', 'type_id'],
                ['e', 'zcode'],
                ['e', 'status'],
                ['e', 'created'],
                ['e', 'changed'],
                ['e', 'title'],
                ['e', 'content'],
                ['e', 'imgs']
            )
            ->from(['entity', 'e'])
            ->leftJoin(
                ['entity_analysis', 'ls'],
                ['ls', 'eid'],
                '=',
                ['e', 'eid']
            );
    }

    protected function _createSolution($rqt_eid, $stype_key, $dst_eid)
    {
        $uid = current_uid();
        $solution = $this->db->select('id')
            ->from('solution')
            ->where('rqt_eid', '=', $rqt_eid, 'int')
            ->andWhere('dst_eid', '=', $dst_eid, 'int')
            ->fetchOne();
        if (!$solution) {
            if (!$this->db->insert('solution')
                ->value('rqt_eid', $rqt_eid, 'int')
                ->value('stype_id', get_type_id($stype_key), 'int')
                ->value('dst_eid', $dst_eid, 'int')
                ->execute()
            ) {
                $this->rollback();
                _debug();
                return false;
            }
            return true;
        }

        $solution_id = $solution->id;

        $vote = $this->db->select('status')
            ->from('solution_vote')
            ->where('solution_id', '=', $solution_id, 'int')
            ->andWhere('uid', '=', $uid, 'int')
            ->fetchOne();

        if (!$vote) {
            $this->voteSolution($solution_id, $uid);
        }

        if ($vote->status == 0) {
            if (!$this->db->update('solution_vote')
                ->where('solution_id', '=', $solution_id, 'int')
                ->andWhere('uid', '=', $uid, 'int')
                ->set('status', 1, 'int')
                ->execute()
            ) {
                $this->rollback();
                _debug();
                return false;
            }
            return true;
        }
        return true;
    }

    protected function _createProperty($product_eid, $ptype_key, $dst_eid, $extra = [])
    {
        $uid = current_uid();
        $property = $this->db->select('id')
            ->from('property')
            ->where('product_eid', '=', $product_eid, 'int')
            ->andWhere('dst_eid', '=', $dst_eid, 'int')
            ->fetchOne();

        if (!$property) {
            if (!$this->db->insert('property')
                ->value('product_eid', $product_eid, 'int')
                ->value('ptype_id', get_type_id($ptype_key), 'int')
                ->value('dst_eid', $dst_eid, 'int')
                ->execute()
            ) {
                $this->rollback();
                _debug();
                return false;
            }
            $property_id = $this->db->lastInsertId();
            if (!$this->voteProperty($property_id, $uid)) {
                $this->rollback();
                _debug();
                return false;
            }
            if ($extra) {
                $p_branch_id = prop($extra, 'branch_id', 0);
                $p_tag_id = prop($extra, 'tag_id', 0);
                $p_target_id = prop($extra, 'target_id', 0);
                if (!$this->db->insert('property_extra')
                    ->value('p_branch_id', $p_branch_id, 'int')
                    ->value('p_tag_id', $p_tag_id, 'int')
                    ->value('p_target_id', $p_target_id, 'int')
                    ->execute()
                ) {
                    $this->rollback();
                    _debug();
                    return false;
                }
            }
            return true;
        }

        $property_id = $property->id;
        $vote = $this->db->select('status')
            ->from('property_vote')
            ->where('property_id', '=', $property_id, 'int')
            ->andWhere('uid', '=', $uid, 'int')
            ->fetchOne();

        if (!$vote) {
            $this->voteProperty($property_id, $uid);
        }

        if ($vote->status == 0) {
            if (!$this->db->update('property_vote')
                ->where('property_id', '=', $property_id, 'int')
                ->andWhere('uid', '=', $uid, 'int')
                ->set('status', 1, 'int')
                ->execute()
            ) {
                $this->rollback();
                _debug();
                return false;
            }
            return true;
        }
        return true;
    }

    protected function voteSolution($solution_id, $uid)
    {
        return $this->db->insert('solution_vote')
            ->value('solution_id', $solution_id, 'int')
            ->value('uid', $uid, 'int')
            ->execute();
    }

    protected function voteProperty($property_id, $uid)
    {
        return $this->db->insert('property_vote')
            ->value('property_id', $property_id, 'int')
            ->value('uid', $uid, 'int')
            ->execute();
    }

    protected function submitEntity($eid, $data, $imgs = '')
    {
        if (!$eid = (int) $eid) {
            _debug('eid must be positive integer');
        }

        if (!$uid = current_uid()) {
            _debug('uid must be positive integer');
        }

        if (!$this->db->insert('e_submit')
            ->value('uid', current_uid(), 'int')
            ->value('eid', $eid, 'int')
            ->value('data', json_encode($data))
            ->value('imgs', json_encode($imgs))
            ->execute()
        ) {
            _debug('submitEntity e_submit insert failed');
        }
        $submit_id = $this->db->lastInsertId();
        $this->last_submit_id = $submit_id;
        return $submit_id;
    }

    protected function createEntity($type_key, $data)
    {
        $current_uid = current_uid();

        $isb = $this->db->insert('entity')
            ->value('type_id', get_type_id($type_key), 'int')
            ->value('zcode', $this->generateZcode())
            ->value('status', 1, 'int')
            ->value('owner_uid', $current_uid)
            ->value('uid', $current_uid, 'int')
            ->value('title', prop($data, 'title', ''))
            ->value('content', prop($data, 'content', ''));

        if ($imgs = $this->extractImgs(prop($data, 'content', ''))) {
            $isb->value('imgs', json_encode($imgs));
        }
        if (!$isb->execute()) {
            _debug('createEntity insert-entity failed');
        }

        $eid = $this->db->lastInsertId();

        $submit_id = $this->submitEntity($eid, $data, $imgs);

        $this->last_eid = $eid;
    }

    protected function updateEntity($eid, $data)
    {
        $eid = (int) $eid;
        if ($eid <= 0) {
            _debug('eid-must-be-positivate-integer');
        }
        $usb = $this->db->update('entity')
            ->where('eid', '=', $eid, 'int')
            ->set('uid', current_uid(), 'int')
            ->set('title', prop($data, 'title', ''))
            ->set('content', prop($data, 'content', ''));
        if ($imgs = $this->extractImgs(prop($data, 'content', ''))) {
            $usb->set('imgs', json_encode($imgs));
        }
        $submit_id = $this->submitEntity($eid, $data, $imgs);

        if (!$usb->execute()) {
            _debug('entity update-failed');
        }

        $this->last_eid = $eid;
    }

    protected function createStory($action, $dst_type_key, $dst_eid, $submit_id, $src_eid = 0)
    {
        if (!$this->db->insert('story')
            ->value('action_id', get_action_id($action), 'int')
            ->value('dst_type_id', get_type_id($dst_type_key), 'int')
            ->value('dst_eid', $dst_eid)
            ->value('e_submit_id', (int) $submit_id, 'int')
            ->value('src_eid', $src_eid, 'int')
            ->value('uid', current_uid())
            ->execute()
        ) {
            _debug('createStory story insert failed');
        }
        return $this->db->lastInsertId();
    }

    protected function extractImgs($content)
    {
        if (!$content) {
            return false;
        }

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($content);
        $doc->preserveWhiteSpace = false;
        $imgs = [];
        if ($img_elems = $doc->getElementsByTagName('img')) {
            foreach ($img_elems as $elem) {
                $img = [
                    'dir' => $elem->getAttribute('data-dir'),
                    'name' => $elem->getAttribute('data-name'),
                    'ext' => $elem->getAttribute('data-ext'),
                ];
                if ($protocol = $elem->getAttribute('data-protocol')) {
                    $img['protocol'] = $protocol;
                }
                if ($site = $elem->getAttribute('data-site')) {
                    $img['site'] = $site;
                }
                $imgs[] = $img;
            }
        }

        return $imgs;
    }

    protected function deleteEntity($eid)
    {
        $this->db->beginTransaction();

        if (!$this->db->delete()->from('story')
            ->where('dst_eid', '=', $eid, 'int')
            ->orWhere('src_eid', '=', $eid, 'int')
            ->execute()
        ) {
            $this->db->rollback();
            _debug('delete story failed');
        }
        if (!$this->db->delete()->from('entity')->where('eid', '=', $eid, 'int')->execute()) {
            $this->db->rollback();
            _debug('delete entity failed');
        }
        if (!$this->db->delete()->from('e_submit')->where('eid', '=', $eid, 'int')->execute()) {
            $this->db->rollback();
            _debug('delete e_submit failed');
        }

        if (!$this->db->delete()->from('entity_analysis')->where('eid', '=', $eid, 'int')->execute()) {
            $this->db->rollback();
            _debug('delete entity_analysis failed');
        }

        $this->db->commit();
        return $this->packOk();
    }

    protected function _entity_analysis($eid)
    {
        $eid = (int) $eid;

        if (!$this->db->select('eid')
            ->from('entity_analysis')
            ->where('eid', '=', $eid, 'int')
            ->fetchOne()
        ) {
            $this->db->insert('entity_analysis')->value('eid', $eid)->execute();
        }
    }

    public function schEntitySet($opts = [])
    {
        return $this->dataSet(
            $this->schEntitySsb($opts)
        );
    }

    public function getEntitySet($opts = [])
    {
        return $this->dataSet(
            $this->getEntityBd($opts)
        );
    }

    public function getSubmitSetByEid($eid)
    {
        return $this->dataSet(
            $this->getSubmitBdByEid($eid)
        );
    }

    public function getZcodeByEid($eid)
    {
        return $this->db->select('zcode')
            ->from('entity')
            ->where('eid', '=', $eid)
            ->fetchOne()
            ->zcode;
    }

}
