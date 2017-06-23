<?php

namespace Admin\Ui;

class ArticleCommentController extends AdminControllerBase
{
    public function index()
    {
        $article = $this->getArticleFromParam();
        $comment_set = $this->service('article_comment')->search(['article_id' => $article->id,'status'=>null]);
        $page = $this->request->query->get('page');
        $comment_set = $comment_set->setCurrentPage($page);

        return $this->page('article/comment', [
            'article' => $article,
            'comment_set' => $comment_set,
        ]);
    }

    public function activate()
    {
        $article = $this->getArticleFromParam();
        $comment = $this->getCommentFromParam();
        return $this->page('article/activate-comment', ['comment' => $comment, 'article' => $article]);
    }

    public function activatePost()
    {
        $zcode = $this->request->request->get('zcode');
        $comment_id = $this->request->request->get('comment_id');
        $pack = $this->service('article_comment')->activateComment($comment_id);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-article_comment-index', ['zcode' => $zcode]);
        }
    }

    public function deactivate()
    {
        $article = $this->getArticleFromParam();
        $comment = $this->getCommentFromParam();
        return $this->page('article/deactivate-comment', ['comment' => $comment, 'article' => $article]);
    }

    public function deactivatePost()
    {
        $zcode = $this->request->request->get('zcode');
        $comment_id = $this->request->request->get('comment_id');
        $pack = $this->service('article_comment')->deactivateComment($comment_id);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-article_comment-index', ['zcode' => $zcode]);
        }
    }

    protected function getArticleFromParam()
    {
        $zcode = $this->getParam('zcode');
        return $this->service('article')->getArticleByZcode($zcode);
    }

    protected function getCommentFromParam()
    {
        $comment_id = $this->getParam('comment_id');
        return $this->service('article_comment')->findCommentById($comment_id);
    }

}