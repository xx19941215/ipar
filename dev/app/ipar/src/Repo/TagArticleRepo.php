<?php
namespace Ipar\Repo;

use Gap\Repo\RepoBase;
use Mars\Repo\TagTableRepo;

class TagArticleRepo extends RepoBase
{
    public function search($query = [])
    {
        $tag_zcode = prop($query, 'tag_zcode', '');
        $tag_id = prop($query, 'tag_id', '');
        $ssb = $this->db->select(['a','*'])
            ->setDto('article')
            ->from(['article_tag', 'at'])
            ->leftJoin(
                ['article', 'a'],
                ['at', 'article_id'],
                '=',
                ['a', 'id']
            );
        if ($tag_id) {
            $ssb->where('tag_id', '=', $tag_id);
        }
        if ($tag_zcode) {
            $ssb->leftJoin(
                ['tag', 't'],
                ['t', 'id'],
                '=',
                ['at', 'tag_id']
            )
                ->where(['t', 'zcode'], '=', $tag_zcode);
        }

        return $this->dataSet($ssb);
    }
}