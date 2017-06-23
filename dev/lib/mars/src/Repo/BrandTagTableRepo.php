<?php
namespace Mars\Repo;

class BrandTagTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'brand_tag';
    protected $dto = 'brand_tag';
    protected $fields = [
        'tag_id' => 'int',
    ];

    protected function validate($data)
    {
        return true;
    }
}