<?php

namespace Ipar\Repo;

class FeatureRepo extends \Mars\Repo\EntityRepo
{
    public function getFeatureByZcode($zcode)
    {
        return $this->getEntityByZcode($zcode);
    }

    public function createFeature($title, $content = '')
    {
        $this->db->beginTransaction();

        $this->createEntity('feature', ['title' => $title, 'content' => $content]);

        $feature_eid = $this->lastEid();
        $this->createStory('create', 'feature', $feature_eid, $this->lastSubmitId());

        $this->db->commit();
        return $this->packItem('eid', $feature_eid);
    }

    public function updateFeature($eid, $title, $content = '')
    {
        $this->db->beginTransaction();

        $this->updateEntity($eid, ['title' => $title, 'content' => $content]);

        $this->createStory('update', 'feature', $eid, $this->lastSubmitId());

        $this->db->commit();
        return $this->packItem();
    }

    public function deleteFeature($eid)
    {
        $this->db->beginTransaction();

        try {
            $this->deleteEntity($eid);
            $this->db->delete('propery')->where('dst_eid', '=', $eid, 'int');
        } catch (\Exception $e) {
            $this->db->rollback();
            return $this->packError('exception', $e->getMessage());
        }

        $this->db->commit();
        return $this->packOk();
    }

    public function schFproductSsb($feature_eid, $opts = [])
    {
        //$query = '', $status = 1
        $query = prop($opts, 'query', '');
        $status = prop($opts, 'status', 1);

        $ssb = $this->db->select()
            ->from(['property', 'p'])
            ->leftJoin(
                ['entity', 'e'],
                ['e', 'eid'],
                '=',
                ['p', 'product_eid']
            )
            ->where(['p', 'product_eid'], '>', 0, 'int')
            ->andWhere(['p', 'dst_eid'], '=', $feature_eid, 'int');

        if ($query) {
            $match_against = 'MATCH(`m`.`title`, `m`.`content`) against(:query IN BOOLEAN MODE)';
            $ssb->andWhereRaw($match_against)
                ->bindValue(':query', $query)
                ->orderByRaw($match_against . ' DESC');
        }

        $ssb->setDto('product');

        if ($status !== null) {
            $ssb->andWhere(['e', 'status'], '=', $status, 'int');
        }

        $ssb->orderBy(['p', 'changed'], 'DESC');
        return $ssb;
    }

    public function countFproduct($feature_eid)
    {
        $count = $this->db->select()
            ->from('property')
            ->where('dst_eid', '=', $feature_eid, 'int')
            ->count();
        $this->_feature_analysis($feature_eid);
        $this->db->update('feature_analysis')->where('feature_eid', '=', $feature_eid, 'int')->set('product_count', $count, 'int')->execute();
        return $count;
    }

    public function schFeatureSet($opts)
    {
        return $this->dataSet(
            $this->schEntitySsb($opts)
        );
    }

    public function getFeatureSet($status = 1)
    {
        return $this->dataSet(
            $this->buildFeatures($status)
        );
    }

    public function schFproductSet($feature_eid, $opts = [])
    {
        return $this->dataSet(
            $this->schFproductSsb($feature_eid, $opts)
        );
    }

    protected function _feature_analysis($feature_eid)
    {
        if (!$this->db->select('feature_eid')
            ->from('feature_analysis')
            ->where('feature_eid', '=', $feature_eid, 'int')
            ->fetchOne()
        ) {
            $this->db->insert('feature_analysis')->value('feature_eid', $feature_eid)->execute();
        }
    }
}
