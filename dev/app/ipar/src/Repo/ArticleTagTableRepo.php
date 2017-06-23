<?php
namespace Ipar\Repo;

use Gap\Repo\TableRepoBase;

class ArticleTagTableRepo extends TableRepoBase
{
    protected $table_name = 'article_tag';
    protected $dto = 'article_tag';
    protected $fields = [
        'id' => 'int',
        'article_id' => 'int',
        'tag_id' => 'int',
        'vote_count' => 'int',
    ];

    public function validate($data)
    {

    }
}