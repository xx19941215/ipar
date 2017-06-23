<?php
namespace Ipar\Service;

//use Gap\Validation\ValidationException;

class RqtService extends \Mars\Service\EntityService
{

    protected $rqt_repo;

    public function bootstrap()
    {
        $this->rqt_repo = $this->repo('rqt');
        parent::bootstrap();
    }

    public function getRqtByEid($eid)
    {
        return $this->rqt_repo->getRqtByEid($eid);
    }

    public function getRqtByZcode($zcode)
    {
        return $this->rqt_repo->getRqtByZcode($zcode);
    }

    public function createRqt($title, $content = '')
    {
        if (true !== ($validated = $this->validateRqt($title))) {
            return $validated;
        }
        return $this->rqt_repo->createRqt($title, $content);
    }

    public function updateRqt($eid, $title, $content = '')
    {
        $eid = (int) $eid;
        if (true !== ($validated = $this->validateRqt($title))) {
            return $validated;
        }
        //$this->deleteCachedEntity($eid);
        return $this->rqt_repo->updateRqt($eid, $title, $content);
    }

    // deprecated
    public function deleteRqt($eid)
    {
        return $this->deleteRqtByEid($eid);
    }

    public function deleteRqtByEid($eid)
    {
        $eid = (int) $eid;
        if (!$eid) {
            return $this->packError('eid', 'not-positive');
        }
        return $this->rqt_repo->deleteRqtByEid($eid);
    }

    public function schRqtSet($opts = [])
    {
        return $this->rqt_repo->schRqtSet($opts);
    }

    //
    // count
    //
    public function countSolution($rqt_eid)
    {
        return $this->rqt_repo->countSolution($rqt_eid);
    }
    public function countSidea($rqt_eid)
    {
        $cache_key = "count-sidea-{$rqt_eid}";
        if ($count = $this->cache()->get($cache_key)) {
            return $count;
        }
        $count = $this->rqt_repo->countSidea($rqt_eid);
        $this->cache()->set($cache_key, $count, 3600);
        return $count;
    }
    public function countSproduct($rqt_eid)
    {
        $cache_key = "count-sproduct-{$rqt_eid}";
        if ($count = $this->cache()->get($cache_key)) {
            return $count;
        }
        $count = $this->rqt_repo->countSproduct($rqt_eid);
        $this->cache()->set($cache_key, $count, 3600);
        return $count;
    }
    public function countSinvent($rqt_eid)
    {
        return $this->rqt_repo->countSinvent($rqt_eid);
    }

    /*
     * create Solution
     */

    public function createSolution($rqt_eid, $type_key, $dst_eid)
    {
        $rqt_eid = (int) $rqt_eid;
        $dst_eid = (int) $dst_eid;
        return $this->rqt_repo->createSolution($rqt_eid, $type_key, $dst_eid);
    }

    public function createSidea($rqt_eid, $content)
    {
        $rqt_eid = (int) $rqt_eid;
        if (!is_string($content) || empty($content)) {
            return $this->packNotEmpty('content');
        }
        return $this->rqt_repo->createSidea($rqt_eid, $content);
    }

    public function createSproduct($rqt_eid, $title, $content, $url = '')
    {
        $rqt_eid = (int) $rqt_eid;
        if (true !== ($validated = $this->validateProduct($title, $content, $url))) {
            return $validated;
        }
        return $this->rqt_repo->createSproduct($rqt_eid, $title, $content, $url);
    }

    public function createSinvent($rqt_eid, $title, $content, $progress)
    {
        $rqt_eid = (int) $rqt_eid;
        if (!is_string($title) || empty($title)) {
            return $this->packNotEmpty('content');
        }
        if (!is_string($content) || empty($content)) {
            return $this->packNotEmpty('content');
        }
        return $this->rqt_repo->createSinvent($rqt_eid, $title, $content, $progress);
    }

    /*
     * Search Solution
     */

    public function schSolutionSet($rqt_eid, $opts = [])
    {
        //$query = '', $stype = '', $status = 1
        $rqt_eid = (int) $rqt_eid;
        return $this->rqt_repo->schSolutionSet($rqt_eid, $opts);
    }

    public function schSideaSet($rqt_eid, $opts = [])
    {
        //$query, $status = 1
        $opts['stype_key'] = 'idea';
        return $this->schSolutionSet($rqt_eid, $opts);
    }

    public function schSproductSet($rqt_eid, $opts = [])
    {
        $opts['stype_key'] = 'product';
        return $this->schSolutionSet($rqt_eid, $opts);
    }

    public function isVoted($property_id, $uid)
    {
        return $this->rqt_repo->ifVoted($property_id, $uid);
    }

    public function getRqtSet($opts = [])
    {
        return $this->rqt_repo->getRqtSet($opts);
    }

    //
    // protected functions
    //
}
