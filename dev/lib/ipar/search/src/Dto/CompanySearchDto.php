<?php
namespace Ipar\Search\Dto;

class CompanySearchDto extends IparSearchDtoBase
{
    public $gid;
    public $type_id;
    public $zcode;

    public $name;
    public $content;

    public $fullname;
    public $status;
    public $logo;

    protected $highlights = [
        'name' => 1,
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
