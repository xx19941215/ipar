<?php
namespace Mars\Service;

class EntityLikeService extends MarsServiceBase
{
    protected $entity_like_repo;

    public function bootstrap()
    {
        $this->entity_like_repo = $this->repo('entity_like');
    }

    public function like($eid)
    {
        $eid = (int)$eid;
        if (!$eid) {
            return $this->packError('eid', 'empty');
        }
        return $this->entity_like_repo->like($eid);
    }

    public function unlike($eid)
    {
        $eid = (int)$eid;
        if (!$eid) {
            return $this->packError('eid', 'empty');
        }
        return $this->entity_like_repo->unlike($eid);
    }

    public function isLike($query = [])
    {
        return $this->entity_like_repo->findEntityLike($query);
    }

    public function schEntityLikeSet($query = [])
    {
        return $this->entity_like_repo->schEntityLikeSet($query);
    }
}
