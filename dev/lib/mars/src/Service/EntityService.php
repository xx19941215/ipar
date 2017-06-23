<?php
namespace Mars\Service;

class EntityService extends MarsServiceBase {
    //
    // public functions
    //

    protected $entity_repo;

    public function bootstrap()
    {
        $this->entity_repo = $this->repo('entity');
    }

    public function schEntitySet($opts = [])
    {
        return $this->entity_repo->schEntitySet($opts);
    }

    public function getEntitySet($opts = [])
    {
        return $this->entity_reop->getEntitySet($opts);
    }

    public function getEntityByEid($eid)
    {
        if (!($eid = (int) $eid)) {
            return null;
        }

        return $this->entity_repo->getEntityByEid($eid);
        /*
        return $this->getCachedEntity(
            $eid,
            function () use($eid) {
                return $this->entity_repo->getEntityByEid($eid);
            }
        );
         */
    }

    public function getEntitiesByEids($eids)
    {
        return $this->getCachedEntities(
            $eids,
            function ($not_cached_eids) {
                return $this->entity_repo->getEntitiesByEids($not_cached_eids);
            }
        );
    }

    public function getEntitiesByObjs($objs)
    {
        $eids = [];
        foreach ($objs as $obj) {
            $eids[] = $obj->eid;
        }
        return $this->getEntitiesByEids($eids);
    }

    /*
    public function countEntityByType($type, $status = 1)
    {
        if ($status === null) {
            return $this->repo($type)->countEntityByType($type, $status);
        }

        $key = "ct_entity_$type";
        $cache = $this->cache();
        if ($ct = $cache->get($key)) {
            return $ct;
        }

        $ct = $this->repo($type)->countEntityByType($type, $status);
        $cache->set($key, $ct);
        return $ct;
    }
     */

    /*
    public function incrEntityByType($type)
    {
        $this->cache()->incr("ct_entity_$type");
    }
     */

    /*
    public function decrEntityByType($type)
    {
        $this->cache()->incr("ct_entity_$type");
    }
     */

    public function activateEntity($eid)
    {
        $eid = (int) $eid;
        if (!$eid) {
            return $this->packError('eid', 'must-be-positive-integer');
        }

        //$this->deleteCachedEntity($eid);
        return $this->entity_repo->activateEntity($eid);
    }

    public function deactivateEntity($eid)
    {
        $eid = (int) $eid;
        if (!$eid) {
            return $this->packError('eid', 'must-be-positive-integer');
        }

        //$this->deleteCachedEntity($eid);
        return $this->entity_repo->deactivateEntity($eid);
    }

    public function deleteEntity($eid)
    {
        $eid = (int) $eid;
        if (!$eid) {
            return $this->packError('eid', 'must-be-positive-integer');
        }

        //$this->deleteCachedEntity($eid);
        return $this->entity_repo->deleteEntity($eid);
    }

    public function getEntities($page)
    {
        $entity_repo = $this->entity_repo;
        list($limit, $offset) = $this->getLimitOffset($page);
        $eids = $entity_repo->getEids($limit, $offset);

        return $this->getCachedEntities(
            $eids,
            function ($not_cached_eids) use ($entity_repo) {
                return $entity_repo->getEntitiesByEids($not_cached_eids);
            }
        );
    }

    public function getSubmitSetByEid($eid)
    {
        $this->entity_repo->getSubmitSetByEid($eid);
    }

    //
    // count
    //
    public function countFollow($eid)
    {
        return $this->entity_repo->countFollow($eid);
    }
    public function countLike($eid)
    {
        return $this->entity_repo->countLike($eid);
    }
    public function countComment($eid)
    {
        return $this->entity_repo->countComment($eid);
    }
    public function countTag($eid)
    {
        return $this->entity_repo->countTag($eid);
    }
    public function countTagVote($eid)
    {
        return $this->entity_repo->countTagVote($eid);
    }
    public function countSubmit($eid)
    {
        return $this->entity_repo->countSubmit($eid);
    }
    public function countSrc($eid)
    {
        return $this->entity_repo->countSrc($eid);
    }
    public function countStory($eid)
    {
        return $this->entity_repo->countStory($eid);
    }

    public function getLatestSubmitId($eid)
    {
        return $this->entity_repo->getLatestSubmitId($eid);
    }

    public function getZcodeByEid($eid)
    {
        return $this->entity_repo->getZcodeByEid($eid);
    }

    //
    // protected functions
    //

    protected function validateProduct($title, $content, $url = '')
    {
        if (!is_string($title) || empty($title)) {
            return $this->packNotEmpty('title');
        }
        if (!is_string($content) || empty($content)) {
            return $this->packNotEmpty('content');
        }
        $title_length = mb_strlen($title);
        if ($title_length < 1 || $title_length > 120) {
            return $this->packOutLength('title', 1, 120);
        }
        if ($url) {
            if (substr($url, 0, 7) !== 'http://' && substr($url, 0, 8) !== 'https://') {
                $url = 'http://' . $url;
            }
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return $this->packError('url', 'must-be-valid-url');
            }
        }
        return true;
    }

    protected function validateRqt($title)
    {
        if (!is_string($title) || empty($title)) {
            return $this->packNotEmpty('title');
        }
        $title_length = mb_strlen($title);
        if ($title_length < 3 || $title_length > 120) {
            return $this->packOutLength('title', 3, 120);
        }
        return true;

    }

    protected function validateFeature($title)
    {
        if (!is_string($title) || empty($title)) {
            return $this->packNotEmpty('title');
        }
        $title_length = mb_strlen($title);
        if ($title_length < 3 || $title_length > 120) {
            return $this->packOutLength('title', 3, 120);
        }
        return true;

    }

}
