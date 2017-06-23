<?php
namespace Ipar\Search\Dto;

class EntitySearchDto extends IparSearchDtoBase
{
    public $eid;
    public $type_id;
    public $zcode;

    public $owner_uid;
    public $uid;

    public $title;
    public $content;

    public $rank;
    public $status;
    public $imgs;
    public $created;
    public $changed;

    protected $highlights = [
        'title' => 1,
        'content' => 1
    ];

    public function getTypeKey()
    {
        return get_type_key($this->type_id);
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getImgs()
    {
        return json_decode($this->imgs, true);
    }

    public function countComment()
    {
        return service('entity_comment')->countComment([
            'eid' => $this->eid
        ]);
    }
}
