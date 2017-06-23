<?php

namespace Ipar\Service;

class FeatureService extends \Mars\Service\EntityService
{

    protected $feature_repo;

    public function bootstrap()
    {
        $this->feature_repo = $this->repo('feature');
        parent::bootstrap();
    }

    public function createFeature($title, $content = '')
    {
        if (true !== ($validated = $this->validateFeature($title))) {
            return $validated;
        }

        return $this->feature_repo->createFeature($title);
    }

    public function getFeatureByEid($eid)
    {
    }

    public function getFeatureByZcode($zcode)
    {
        return $this->feature_repo->getFeatureByZcode($zcode);
    }

    public function updateFeature($eid,  $title, $content = '')
    {
        $eid = (int) $eid;
        if (true !== ($validated = $this->validateFeature($title))) {
            return $validated;
        }

        return $this->feature_repo->updateFeature($eid, $title, $content);
    }

    public function deleteFeature($eid)
    {
        $eid = (int) $eid;
        if (!$eid) {
            return $this->packError('eid', 'must-be-positive-integer');
        }

        //$this->deleteCachedEntity($eid);

        return $this->feature_repo->deleteFeature($eid);
    }

    public function schFeatureSet($opts = [])
    {
        $opts['type_key'] = 'feature';
        return $this->feature_repo->schFeatureSet($opts);
    }

    public function getFeatureSet($status = 1)
    {
        return $this->feature_repo->getFeatureSet($status);
    }

    public function schFproductSet($feature_eid, $opts = [])
    {
        return $this->feature_repo->schFproductSet($feature_eid, $opts);
    }

    public function countFproduct($feature_eid)
    {
        return $this->feature_repo->countFproduct($feature_eid);
    }
}
