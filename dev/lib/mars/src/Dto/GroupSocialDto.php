<?php
namespace Mars\Dto;

class GroupSocialDto
{
    public $id;
    public $gid;
    public $social_id;
    public $name;
    public $qrcode;
    public $url;
    public $changed;

    public function getQrcode(){
        return json_decode($this->qrcode, true);
    }
}
