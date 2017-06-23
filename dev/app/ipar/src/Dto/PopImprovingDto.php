<?php

namespace Ipar\Dto;


class PopImprovingDto
{
    public $eid;
    public $uid;
    public $created;
    public $content;
    public $title;
    public $src_title;
    public $dst_eid;
    public $src_zcode;

    public function countLike($eid)
    {
        return service('entity')->countLike($eid);
    }

    public function countComment($eid)
    {
        if (empty($eid)) {
            return 0;
        }
        return service('entity_comment')->countComment([
            'eid' => $eid
        ]);
    }

    public function getZcodeByEid($eid)
    {
        return service('entity')->getZcodeByEid($eid);
    }
}
