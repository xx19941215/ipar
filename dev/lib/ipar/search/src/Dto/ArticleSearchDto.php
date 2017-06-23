<?php
namespace Ipar\Search\Dto;

class ArticleSearchDto extends IparSearchDtoBase
{
    public $id;
    public $uid;
    public $zcode;

    public $title;
    public $content;

    public $status;
    public $locale_id;
    public $original_id;

    public $created;
    public $changed;

    protected $highlights = [
        'title' => 1,
        'content' => 1
    ];

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

}
