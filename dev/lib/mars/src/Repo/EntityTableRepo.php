<?php

namespace Mars\Repo;

class EntityTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'entity';
    protected $dto = 'entity';
    protected $fields = [
        'eid' => 'int',
        'type_id' => 'int',
        'zcode' => 'str',
        'owner_uid' => 'int',
        'uid' => 'int',
        'title' => 'str',
        'content' => 'str',
        'imgs' => 'str',
        'status' => 'int',
        'rank' => 'int'
    ];
}
