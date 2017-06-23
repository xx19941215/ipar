<?php
namespace Mars\Dto;

class GroupFollowDto
{
    public $uid;
    public $gid;
    public $created;

    public function getFollowedUser()
    {
        return service('user')->getUserByUid($this->uid);
    }
}
