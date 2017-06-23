<?php
namespace Mars\Dto;

class UserFollowDto
{
    public $uid;
    public $dst_uid;
    public $created;

    public function getFollowedUser()
    {
        return service('user')->getUserByUid($this->uid);
    }

    public function getFollowingUser()
    {
        return service('user')->getUserByUid($this->dst_uid);
    }
}
