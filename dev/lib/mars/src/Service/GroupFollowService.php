<?php
namespace Mars\Service;

class GroupFollowService extends FollowService
{
    public function follow($gid)
    {
        return $this->repo('group_follow')->follow($gid);
    }

    public function isFollowing($query = [])
    {
        return $this->repo('group_follow')->findGroupFollow($query);
    }

    public function unfollow($gid)
    {
        return $this->repo('group_follow')->unfollow($gid);
    }

    public function fetchFollowedSet($gid)
    {
        return $this->repo('group_follow')->fetchFollowedSet($gid);
    }

    public function fetchFollowingSet($uid = '')
    {
        return $this->repo('group_follow')->fetchFollowingSet($uid);
    }
}
