<?php
namespace Ipar\Dto;

class BrandProductVoteDto
{
    public $brand_product_id;
    public $vote_uid;
    public $created;

    public function getVoteUser()
    {
        return service('user')->getUserByUid($this->vote_uid);
    }
}
