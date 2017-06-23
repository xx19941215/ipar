<?php
namespace Mars\Service;

class CommentService extends MarsServiceBase
{
    protected $comment_repo;
    public function bootstrap()
    {
        $this->comment_repo = $this->repo('comment');
    }

    public function search($query = [])
    {
        return $this->comment_repo->search($query);
    }

    public function findCommentById($id)
    {
        $id = (int) $id;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        return $this->comment_repo->findCommentById($id);
    }

    public function createComment($dst_type, $dst_id, $content, $opts = [])
    {
        $dst_type = (int) $dst_type;
        $dst_id = (int) $dst_id;

        if (true !== ($validated = $this->validateComment($content))) {
            return $validated;
        }
        return $this->comment_repo->createComment($dst_type, $dst_id, $content, $opts);
    }

    public function deactivateComment($id)
    {
        $id = (int) $id;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        return $this->comment_repo->changeStatus($id, 0);
    }

    public function activateComment($id)
    {
        $id = (int) $id;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        return $this->comment_repo->changeStatus($id, 1);
    }

    public function deleteCommentById($id)
    {
        $id = (int) $id;

        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        $comment = $this->comment_repo->findCommentById($id);
        $status = (int) $comment->status;

        if ($status !== 0) {
            return $this->packError('comment_status', 'comment-must-be-deactivated-first');
        }

        return $this->comment_repo->deleteCommentById($id);
    }

    public function countComment($query = [])
    {
        return $this->comment_repo->countComment($query);
    }

    protected function validateComment($content)
    {
        if (!is_string($content) || empty($content)) {
            return $this->packEmpty('content');
        }
        return true;
    }
}
?>
