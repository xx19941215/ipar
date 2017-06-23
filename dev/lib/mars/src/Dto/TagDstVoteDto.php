<?php
namespace Mars\Dto;

class TagDstVoteDto
{
    public $tag_dst_id;
    public $vote_uid;
    public $created;

    public function getVoteUser()
    {
        return service('user')->getUserByUid($this->vote_uid);
    }
}
