<?php
namespace Ipar\Repo;

use Gap\Repo\RepoBase;
use Mars\Repo\TagTableRepo;

class ArticleTagRepo extends RepoBase
{
    protected $tag_table_repo;
    protected $article_tag_table_repo;
    protected $article_tag_vote_table_repo;

    public function bootstrap()
    {
        $this->tag_table_repo = new TagTableRepo($this->db);
        $this->article_tag_table_repo = new ArticleTagTableRepo($this->db);
        $this->article_tag_vote_table_repo = new ArticleTagVoteTableRepo($this->db);
    }

    public function delete($data)
    {
        return $this->article_tag_table_repo->delete($data);
    }

    public function save($data = [])
    {
        $this->db->beginTransaction();
        $tag_title = prop($data, 'tag_title');
        $tag_title = trim($tag_title);
        $tag_title = preg_replace('!\s+!', ' ', $tag_title);
        $tag_id = $this->savedTagId($tag_title);
        if (!$tag_id) {
            $this->db->rollback();
            return $this->packError('article_tag', 'create-failed');
        }
        $data['tag_id'] = $tag_id;
        $data['article_tag_id'] = $this->savedArticleTagId($data['article_id'], $data['tag_id']);
        if (!$data['article_tag_id']) {
            $this->db->rollback();
            return $this->packError('article_tag', 'create_failed');
        }
        $data['vote_uid'] = $data['uid'];
        $pack = $this->article_tag_vote_table_repo->create($data);
        if (!$pack->isOk()) {
            $this->db->rollback();
            return $pack;
        }
        $this->db->commit();
        return $this->packOk();
    }

    public function saveMultipleTag($data = [])
    {
        $titles = $data['titles'];
        $titles = trim($titles);
        $titles = preg_replace('!\s+!', ' ', $titles);
        $titles = str_replace("，", ",", $titles);
        $titles = explode(",", $titles);
        foreach ($titles as $title) {
            $data['tag_title'] = $title;
            $pack = $this->save($data);
            if (!$pack->isOk()) {
                return $pack;
            }
        }
        return $this->packOk();
    }


    public function search($data = [])
    {
        $tag_title = prop($data, 'query');
        $ssb = $this->db->select()
            ->setDto('tag')
            ->from(['article_tag', 'a'])
            ->leftJoin(
                ['tag', 't'],
                ['a', 'tag_id'],
                '=',
                ['t', 'id']
            )
            ->where('article_id', '=', $data['article_id'])
            ->andWhere('title', 'LIKE', "%$tag_title%");
        return $this->dataSet($ssb);
    }

    protected function savedTagId($tag_title)
    {
        if ($existed = $this->tag_table_repo->findOne(['title' => $tag_title], ['id'])) {
            return $existed->id;
        }
        $pack = $this->tag_table_repo->create(['title' => $tag_title]);
        if ($pack->isOk()) {
            return $pack->getItem('id');
        }
        return 0;
    }

    protected function savedArticleTagId($article_id, $tag_id)
    {
        if ($existed = $this->article_tag_table_repo->findOne(['article_id' => $article_id, 'tag_id' => $tag_id], ['id'])) {
            return $existed->id;
        }

        $pack = $this->article_tag_table_repo->create([
            'article_id' => $article_id,
            'tag_id' => $tag_id
        ]);

        if ($pack->isOk()) {
            return $pack->getItem('id');
        }

        return 0;
    }

}