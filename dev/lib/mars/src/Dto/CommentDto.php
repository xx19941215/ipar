<?php
namespace Mars\Dto;

class CommentDto
{
    public $id;
    public $eid;
    public $article_id;
    public $uid;
    public $reply_id;
    public $reply_uid;
    public $conv;
    public $status;
    public $created;
    public $content;

    public function getUser()
    {
        return user($this->uid);
    }
}
