<?php
namespace Mars\Repo;

use Gap\Repo\TableRepoBase;

class EntityTagTableRepo extends TableRepoBase
{
    protected $table_name = 'entity_tag';
    protected $dto = 'entity_tag';
    protected $fields = [
        'id' => 'int',
        'entity_type_id'=>'int',
        'eid' => 'int',
        'tag_id' => 'int',
        'vote_count' => 'int',
    ];


    protected function validate($data)
    {

    }

}