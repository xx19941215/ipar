<?php
namespace Ipar\Service;

class ArticleService extends IparServiceBase
{
    protected $article_repo;

    public function bootstrap()
    {
        $this->article_repo = $this->repo('article');
    }

    public function getArticleById($id)
    {
        $id = (int) $id;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        return $this->article_repo->getArticleById($id);
    }

    public function getArticleByZcode($zcode)
    {
        return $this->article_repo->getArticleByZcode($zcode);
    }

    public function getArticleSet($locale_id='')
    {
        return $this->article_repo->getArticleSet($locale_id);
    }

    public function getArticleSetAll()
    {
        return $this->article_repo->getArticleSetAll();
    }

    public function createArticle($title, $content, $locale_id, $original_id='')
    {
        if (!$title) {
            return $this->packNotEmpty('title');
        }

        if (!$content) {
            return $this->packNotEmpty('content');
        }

        return $this->article_repo->createArticle($title, $content, $locale_id, $original_id);
    }

    public function deactivate($id)
    {
        $article = $this->getArticleById($id);
        $id = (int) $id;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        return $this->article_repo->updateArticle($id, ['status' => 0]);
    }

    public function activate($id)
    {
        $article = $this->getArticleById($id);
        $id = (int) $id;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        return $this->article_repo->updateArticle($id, ['status' => 1]);
    }

    public function deleteArticle($id)
    {
        $article = $this->getArticleById($id);
        $id = (int) $id;
        $status = (int) $article->status;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        if ($status !== 0) {
            return $this->packError('article_status', 'article-must-be-deactivated-first');
        }

        return $this->article_repo->deleteArticle($id);
    }

    public function schArticleSet($opts = [])
    {
        return $this->article_repo->schArticleSet($opts);
    }

    public function editArticle($id, $opts)
    {
        $id = (int) $id;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        return $this->article_repo->updateArticle($id, $opts);
    }

    public function deleteLocale($id)
    {
        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }
        return $this->article_repo->updateArticle($id, [
            'original_id' => $id
        ]);
    }

    public function contentExtractImgs()
    {
        return $this->article_repo->contentExtractImgs();
    }
}
?>
