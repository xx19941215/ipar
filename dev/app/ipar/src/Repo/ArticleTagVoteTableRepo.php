<?php
namespace Ipar\Repo;

use Gap\Repo\TableRepoBase;

class ArticleTagVoteTableRepo extends TableRepoBase
{
    protected $table_name = 'article_tag_vote';
    protected $dto = 'article_tag_vote';
    protected $fields = [
        'article_tag_id' => 'int',
        'vote_uid' => 'int',
    ];

    public function validate($data)
    {
        if ($existed = $this->findOne(['article_tag_id' => $data['article_tag_id'], 'vote_uid' => $data['vote_uid']])) {
            $this->addError('vote', 'already-exists');
        }

    }
}