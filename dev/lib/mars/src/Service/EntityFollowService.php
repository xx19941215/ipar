<?php
namespace Mars\Service;

class EntityFollowService extends FollowService
{
    protected $entity_follow_repo;

    public function bootstrap()
    {
        $this->entity_follow_repo = $this->repo('entity_follow');
    }

    public function follow($eid)
    {
        $eid = (int)$eid;

        if (!service('entity')->getEntityByEid($eid)) {
            return $this->packError('eid', 'not-found');
        }

        if ($this->entity_follow_repo->isFollowing($eid)) {
            return $this->packError('eid', 'you-had-follow-the-entity');
        }
        $follow = $this->entity_follow_repo->follow($eid);
        if (!$follow) {
            return $this->packError('eid', 'create-entity_follow-failed');
        }
        return $follow;
    }

    public function unfollow($eid)
    {
        $eid = (int)$eid;

        if (!service('entity')->getEntityByEid($eid)) {
            return $this->packError('eid', 'not-found');
        }

        if (!$this->entity_follow_repo->isFollowing($eid)) {
            return $this->packError('eid', 'you-had-follow-the-entity');
        }
        $unfollow = $this->entity_follow_repo->unfollow($eid);
        if (!$unfollow) {
            return $this->packError('eid', 'create-entity_follow-failed');
        }
        return $unfollow;
    }

    public function isFollowing($eid)
    {
        $eid = (int)$eid;
        if (!service('entity')->getEntityByEid($eid)) {
            return $this->packError('eid', 'not-found');
        }
        return $this->entity_follow_repo->isFollowing($eid);
    }

    public function countFollowed($eid)
    {
        $eid = (int)$eid;
        if (!service('entity')->getEntityByEid($eid)) {
            return 0;
            //return $this->packError('eid', 'not-found');
        }
        return $this->entity_follow_repo->countFollowed($eid);
    }
    public function countFollowing($uid = '')
    {
        $uid = $uid ? $uid : service('user')->getCurrentUid();
        if (!service('user')->getUserByUid($uid)) {
            return $this->packError('uid', 'not-found');
        }
        return $this->entity_follow_repo->countFollowing($uid);
    }

    public function fetchFollowedSet($eid)
    {
        $eid = (int)$eid;
        if (!service('entity')->getEntityByEid($eid)) {
            return $this->packError('eid', 'not-found');
        }
        return $this->entity_follow_repo->fetchFollowedSet($eid);

    }
    public function fetchFollowingSet($uid = '')
    {
        $uid = $uid ? $uid : service('user')->getCurrentUid();
        if (!service('user')->getUserByUid($uid)) {
            return $this->packError('uid', 'not-found');
        }
        return $this->entity_follow_repo->fetchFollowingSet($uid);
    }
}
