<?php
namespace Mars\Repo;

class IndustryTagTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'industry_tag';
    protected $dto = 'industry_tag';
    protected $fields = [
        'tag_id' => 'int',
    ];

    protected function validate($data)
    {

    }
}