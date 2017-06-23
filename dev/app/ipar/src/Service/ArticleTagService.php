<?php
namespace Ipar\Service;

class ArticleTagService extends IparServiceBase
{
    protected $article_tag_repo;

    public function bootstrap()
    {
        $this->article_tag_repo = $this->repo('article_tag');
    }

    public function save($data)
    {
        return $this->article_tag_repo->save($data);
    }

    public function delete($data)
    {
        return $this->article_tag_repo->delete($data);
    }

    public function saveMultipleTag($data)
    {
        return $this->article_tag_repo->saveMultipleTag($data);
    }

    public function search($data)
    {
        return $this->article_tag_repo->search($data);
    }

}
