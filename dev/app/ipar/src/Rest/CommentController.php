<?php
namespace Ipar\Rest;

class CommentController extends \Gap\Routing\Controller
{
    protected $show_comment_count = 8;

    protected $more_comment_count = 5;

    public function getLatestCommentsPost()
    {
        $query = [
            'dst_type' => $this->request->request->get('dst_type'),
            'dst_id' => $this->request->request->get('dst_id'),
        ];
        $comment_set = $this->service('comment')->search($query);
        $comments = $comment_set
            ->setCountPerPage($this->show_comment_count)
            ->getItems();

        return $this->makePack($comments);
    }

    public function getLaterCommentsPost()
    {
        $query = [
            'dst_type' => $this->request->request->get('dst_type'),
            'dst_id' => $this->request->request->get('dst_id'),
            'latest_id' => $this->request->request->get('latest_id')
        ];
        $comment_set = $this->service('comment')->search($query);
        $comments = $comment_set
            ->setCountPerPage($this->more_comment_count)
            ->getItems();

        return $this->makePack($comments);
    }

    public function getEarlierCommentsPost()
    {
        $query = [
            'dst_type' => $this->request->request->get('dst_type'),
            'dst_id' => $this->request->request->get('dst_id'),
            'oldest_id' => $this->request->request->get('oldest_id')
        ];
        $comment_set = $this->service('comment')->search($query);
        $comments = $comment_set
            ->setCountPerPage($this->more_comment_count)
            ->getItems();

        return $this->makePack($comments);
    }

    public function convPost()
    {
        $query = [
            'conv' => $this->request->request->get('conv')
        ];
        $comment_set = $this->service('comment')->search($query);
        $comments = $comment_set->getItems();

        return $this->makePack($comments);
    }

    public function createCommentPost()
    {
        $opts = [];

        $dst_type = $this->request->request->get('dst_type_id');
        $dst_id = $this->request->request->get('dst_id');
        $content = $this->request->request->get('content');

        $reply_id = $this->request->request->get('reply_id');
        $reply_uid = $this->request->request->get('reply_uid');


        if ($reply_id && $reply_uid) {
            $opts['reply_id'] = $reply_id;
            $opts['reply_uid'] = $reply_uid;

            $reply_comment = $this->service('comment')->findCommentById($reply_id);
            $opts['conv'] = $reply_comment->conv;
        }

        $pack = $this->service('comment')->createComment($dst_type, $dst_id, $content, $opts);

        return $pack;
    }

    public function deleteCommentPost()
    {
        $comment_id = $this->request->request->get('id');

        $comment = $this->service('comment')->findCommentById($comment_id);

        if ($comment->uid == current_uid()) {
            $pack = $this->service('comment')->deactivateComment($comment_id);
            return $pack;
        }

        return $this->packError('comment', 'delete');
    }

    protected function makePack($comments)
    {
        $arr = [];

        foreach ($comments as $comment) {
            $item = [];
            $user = $comment->getUser();

            $item['user_nick'] = $user->nick;
            $item['user_url'] = $user->getUrl();
            $item['uid'] = $user->uid;

            if ($avt = $user->getAvt()) {
                $item['user_avt'] = '<img src="' . img_src($avt, 'small') . '">';
            } else {
                $item['user_avt'] = '<i class="icon icon-avt"></i>';
            }

            $item['id'] = $comment->id;
            $item['content'] = $comment->content;
            $item['created'] = time_elapsed_string($comment->created);

            if ($comment->reply_id) {
                $item['reply_id'] = $comment->reply_id;
                $item['reply_uid'] = $comment->reply_uid;
                $item['reply_user_nick'] = user($comment->reply_uid)->nick;
                $item['conv'] = $comment->conv;
            }

            $arr[] = $item;
        }

        return $this->packItem('comments', $arr);
    }
}
