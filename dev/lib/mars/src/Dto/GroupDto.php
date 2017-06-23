<?php
namespace Mars\Dto;

class GroupDto
{
    public $gid;
    public $uid;
    public $zcode;
    public $name;
    public $fullname;
    public $content;
    public $logo;
    public $website;
    public $size_range_id; // todo need to find more proper name
    public $status;
    public $established;
    public $created;
    public $changed;

    public function getLogo()
    {
        return json_decode($this->logo, true);
    }

    public function getContentAbbr()
    {
        return mb_substr($this->content, 0, 150);
    }
}
