<?php
namespace Mars\Repo;

class EntityTagVoteTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'entity_tag_vote';
    protected $dto = 'entity_tag_vote';
    protected $fields = [
        'entity_tag_id' => 'int',
        'vote_uid' => 'int',
    ];


    protected function validate($data)
    {
        if ($existed = $this->findOne(['entity_tag_id' => $data['entity_tag_id'], 'vote_uid' => $data['vote_uid']])) {
            $this->addError('vote', 'already-exists');
        }
    }
}
