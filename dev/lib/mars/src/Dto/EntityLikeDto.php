<?php
namespace Mars\Dto;

class EntityLikeDto
{
    public $eid;
    public $uid;
    public $created;

    public function getLikeUser()
    {
        return service('user')->getUserByUid($this->uid);
    }
}
