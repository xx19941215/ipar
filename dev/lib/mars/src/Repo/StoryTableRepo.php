<?php

namespace Mars\Repo;

class StoryTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'story';
    protected $dto = 'story';
    protected $fields = [
        'id' => 'int',
        'uid' => 'int',
        'action_id' => 'int',
        'dst_type_id' => 'int',
        'dst_eid' => 'int',
        'e_submit_id' => 'int',
        'src_eid' => 'int'
    ];
}
