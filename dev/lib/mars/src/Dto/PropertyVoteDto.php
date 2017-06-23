<?php
namespace Mars\Dto;

class PropertyVoteDto
{
    public $property_id;
    public $uid;
    public $status;
    public $changed;

    public function getVoteUser()
    {
        return service('user')->getUserByUid($this->uid);
    }
}
