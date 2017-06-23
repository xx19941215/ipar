<?php
namespace Mars\Service;

class ArticleCommentService extends \Gap\Service\ServiceBase
{
    protected $article_comment_repo;

    public function bootstrap()
    {
        $this->article_comment_repo = $this->repo('article_comment');
    }

    public function createComment($data = [])
    {

        if (true !== ($validated = $this->validateComment($data['content']))) {
            return $validated;
        }

        return $this->article_comment_repo->createComment($data);
    }

    public function search($query = [])
    {
        return $this->article_comment_repo->search($query);
    }

    public function findCommentById($id)
    {
        $id = (int)$id;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        return $this->article_comment_repo->findCommentById($id);
    }


    public function deactivateComment($id)
    {
        $id = (int)$id;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        return $this->article_comment_repo->changeStatus($id, 0);
    }

    public function activateComment($id)
    {
        $id = (int)$id;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer'); }

        return $this->article_comment_repo->changeStatus($id, 1);
    }

    public function deleteCommentById($id)
    {
        $id = (int)$id;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        $comment = $this->article_comment_repo->findCommentById($id);
        $status = (int)$comment->status;

        if ($status !== 0) {
            return $this->packError('comment_status', 'comment-must-be-deactivated-first');
        }

        return $this->article_comment_repo->deleteCommentById($id);
    }

    public function countComment($query = [])
    {
        return $this->article_comment_repo->countComment($query);
    }

    protected function validateComment($content)
    {
        if (!is_string($content) || empty($content)) {
            return $this->packEmpty('content');
        }
        return true;
    }
}