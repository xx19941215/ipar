<?php
namespace Mars\Dto;

class UserCommentDto extends CommentDto {
    public $user_nick;
    public $user_zcode;
    public $user_avt_json;

    public $reply_nick;
    public $reply_zcode;
}
