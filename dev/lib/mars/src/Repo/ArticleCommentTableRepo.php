<?php

namespace Mars\Repo;

class ArticleCommentTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'article_comment';
    protected $dto = 'comment';
    protected $fields = [
        'id' => 'int',
        'uid' => 'str',
        'article_id' => 'int',
        'reply_uid' => 'int',
        'reply_id' => 'int',
        'conv' => 'int',
        'content' => 'str',
        'status' => 'int',
    ];

    public function findCommentById($id)
    {
        return $this->db->select()
            ->from('article_comment')
            ->where('id', '=', $id)
            ->setDto('comment')
            ->fetchOne();
    }

    public function search($query = [])
    {
        return $this->dataSet(
            $this->schCommentSsb($query)
        );
    }

    public function schCommentSsb($query = [])
    {
        $status = prop($query, 'status', 1);
        $article_id = prop($query, 'article_id', '');
        $reply_id = prop($query, 'reply_id', '');
        $reply_uid = prop($query, 'reply_uid', '');
        $conv = prop($query, 'conv', '');
        $latest_id = prop($query, 'latest_id', '');
        $oldest_id = prop($query, 'oldest_id', '');

        $ssb = $this->db->select()
            ->from('article_comment')
            ->orderBy('created', 'DESC');

        if ($status !== null) {
            $ssb->where('status', '=', $status, 'int');
        }

        if ($article_id) {
            $ssb->andWhere('article_id', '=', $article_id, 'int');
        }

        if ($reply_id) {
            $ssb->andWhere('reply_id', '=', $reply_id, 'int');
        }

        if ($reply_uid) {
            $ssb->andWhere('reply_uid', '=', $reply_uid, 'int');
        }

        if ($conv) {
            $ssb->andWhere('conv', '=', $conv, 'int');
        }

        if ($latest_id) {
            $ssb->andWhere('id', '>', $latest_id, 'int');
        }

        if ($oldest_id) {
            $ssb->andWhere('id', '<', $oldest_id, 'int');
        }

        $ssb->setDto('comment');

        return $ssb;
    }

    public function createComment($data = [])
    {
        $this->db->beginTransaction();

        $uid = prop($data, 'uid');
        $article_id = prop($data, 'article_id');
        $content = prop($data, 'content');

        $reply_id = prop($data['opts'], 'reply_id');
        $reply_uid = prop($data['opts'], 'reply_uid');
        $conv = prop($data['opts'], 'conv');

        $isb = $this->db->insert('article_comment')
            ->value('article_id', $article_id, 'int')
            ->value('uid', $uid, 'int')
            ->value('content', $content);

        if ($reply_id) {
            $isb->value('reply_id', $reply_id, 'int')
                ->value('reply_uid', $reply_uid, 'int');
        }

        if ($conv) {
            $isb->value('conv', $conv, 'int');
        }

        if (!$isb->execute()) {
            $this->db->rollback();
            return $this->packError('comment', 'insert-failed');
        }

        $comment_id = $this->db->lastInsertId();

        if (!$conv) {
            $usb = $this->db->update('article_comment')
                ->where('id', '=', $comment_id, 'int')
                ->set('conv', $comment_id, 'int')
                ->execute();

            if (!$usb) {
                $this->db->rollback();
                return $this->packError('comment', 'update-conv-failed');
            }
        }

        $this->db->commit();
        return $this->packOk();
    }

    public function changeStatus($id, $status)
    {
        $usb = $this->db->update('article_comment')
            ->where('id', '=', $id, 'int')
            ->set('status', $status, 'int');

        if ($usb->execute()) {
            return $this->packOk();
        }

        return $this->packError('comment', 'change-status-failed');
    }

    public function deleteCommentById($id)
    {
        $dsb = $this->db->delete()
            ->from('article_comment')
            ->where('id', '=', $id, 'int');

        if ($dsb->execute()) {
            return $this->packOk();
        }

        return $this->packError('comment', 'delete-failed');
    }

    public function countComment($query = [])
    {
        $ssb = $this->schCommentSsb($query);
        return $ssb->count();
    }
}