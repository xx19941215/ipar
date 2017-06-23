<?php
namespace Mars\Service;

class UserFollowService extends FollowService
{
    public function follow($dst_uid)
    {
        $dst_uid = (int)$dst_uid;

        if (!user_service()->getUserByUid($dst_uid)) {
            return $this->packError('uid', 'not-found');
        }

        $userfollow_repo = $this->repo('user_follow');

        if ($userfollow_repo->isFollowing($dst_uid)) {
            return $this->packError('uid', 'you-had-follow-the-user');
        }
        $follow = $userfollow_repo->follow($dst_uid);
        if (!$follow) {
            return $this->packError('uid', 'create-user_follow-failed');
        }
        return $follow;
    }
    public function unfollow($dst_uid)
    {
        $dst_uid = (int)$dst_uid;

        if (!user_service()->getUserByUid($dst_uid)) {
            return $this->packError('uid', 'not-found');
        }

        $userfollow_repo = $this->repo('user_follow');

        if (!$userfollow_repo->isFollowing($dst_uid)) {
            return $this->packError('uid', 'you-had-not-follow-the-user');
        }
        $unfollow = $userfollow_repo->unfollow($dst_uid);
        if (!$unfollow) {
            return $this->packError('uid', 'create-user_follow-failed');
        }

        return $unfollow;
    }
    public function isFollowing($dst_uid)
    {
        $dst_uid = (int)$dst_uid;

        if (!user_service()->getUserByUid($dst_uid)) {
            return $this->packError('uid', 'not-found');
        }
        return $this->repo('user_follow')->isFollowing($dst_uid);
        //return $this->repo('user_follow')->findUserFollow($dst_uid);
    }
    public function countFollowed($dst_uid)
    {
        $dst_uid = (int)$dst_uid;

        if (!user_service()->getUserByUid($dst_uid)) {
            return $this->packError('uid', 'not-found');
        }
        return $this->repo('user_follow')->countFollowed($dst_uid);
    }

    public function countFollowing($dst_uid)
    {
        $dst_uid = (int)$dst_uid;

        if (!user_service()->getUserByUid($dst_uid)) {
            return $this->packError('uid', 'not-found');
        }
        return $this->repo('user_follow')->countFollowing($dst_uid);
    }

    public function fetchFollowedSet($dst_uid)
    {
        $dst_uid = (int)$dst_uid;

        if (!user_service()->getUserByUid($dst_uid)) {
            return $this->packError('uid', 'not-found');
        }

        return $this->repo('user_follow')->fetchFollowedSet($dst_uid);
    }

    public function fetchFollowingSet($uid)
    {
        $uid = (int)$uid;

        if (!user_service()->getUserByUid($uid)) {
            return $this->packError('uid', 'not-found');
        }

        return $this->repo('user_follow')->fetchFollowingSet($uid);
    }

    public function fetchCommonUserSet($dst_uid)
    {
        return $this->repo('user_follow')->fetchCommonUserSet($dst_uid);
    }

    public function fetchPopularUsers($count = 10) {
        return $this->repo('user_follow')->fetchPopularUsers($count);
    }
}
