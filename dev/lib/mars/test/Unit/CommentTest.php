<?php
namespace Mars\Test\Unit;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateComment()
    {
        $comment_service = service('comment');
        $data = ['dst_type' => '1', 'dst_id' => '20', 'content' => 'e20 comment 1'];

        $dst_type = $data['dst_type'];
        $dst_id = $data['dst_id'];
        $content = $data['content'];

        $pack = $comment_service->createComment($dst_type, $dst_id, $content);

        $this->assertTrue($pack->isOk());

        $comment_set = $comment_service->search(['dst_type' => '1', 'dst_id' => '20']);
        $new_comment = $comment_set
            ->setCountPerPage(1)
            ->getItems()[0];

        $this->assertInstanceOf('Mars\Dto\CommentDto', $new_comment);

        $new_comment_id = $new_comment->id;
        $comment_service->deactivateComment($new_comment->id);
        $new_comment = $comment_service->findCommentById($new_comment_id);
        $this->assertEquals(0, $new_comment->status);
        $this->assertEquals($new_comment->id, $new_comment->conv);

        $pack = $comment_service->deleteCommentById($new_comment_id);
        $this->assertTrue($pack->isOk());
    }

    public function testGetPreviousComments()
    {
        $comment_service = service('comment');

        $data = [
            ['dst_type' => '1', 'dst_id' => '20', 'content' => 'e20 comment 1'],
            ['dst_type' => '1', 'dst_id' => '20', 'content' => 'e20 comment 2'],
            ['dst_type' => '1', 'dst_id' => '20', 'content' => 'e20 comment 3'],
            ['dst_type' => '1', 'dst_id' => '20', 'content' => 'e20 comment 4'],
            ['dst_type' => '1', 'dst_id' => '10', 'content' => 'e10 comment 1'],
            ['dst_type' => '1', 'dst_id' => '10', 'content' => 'e10 comment 2'],
            ['dst_type' => '1', 'dst_id' => '10', 'content' => 'e10 comment 3']
        ];

        // insert dummy data
        $dst_type = 1;
        $dst_id = 20;
        $query = ['dst_type' => $dst_type, 'dst_id' => $dst_id];

        $old_count = $comment_service->countComment($query);

        foreach ($data as $c) {
            $comment_service->createComment($c['dst_type'], $c['dst_id'], $c['content']);
            sleep(1);
        }

        $new_count = $comment_service->countComment($query);

        $this->assertEquals($new_count - $old_count, 4);

        // search all comments for on entity
        $comments = $comment_service->search($query)->getItems();

        foreach ($comments as $comment) {
            $this->assertInstanceOf('Mars\Dto\CommentDto', $comment);
            $this->assertEquals(20, (int) $comment->dst_id);
        }

        // search latest comment
        $latest_id = $comments[3]->id;
        $latest_comments = $comment_service
            ->search([
                'latest_id' => $latest_id,
                'dst_type' => 1,
                'dst_id' => 20,
            ])
            ->getItems();

        $this->assertEquals($latest_comments[0]->content, 'e20 comment 4');
        $this->assertEquals($latest_comments[1]->content, 'e20 comment 3');

        // search dummy data and delete
        $comments = $comment_service->search($query)->getItems();

        $query['dst_id'] = 10;
        $comments = array_merge($comments, $comment_service->search($query)->getItems());

        foreach ($comments as $comment) {
            $comment_service->deactivateComment($comment->id);
            $comment_service->deleteCommentById($comment->id);
        }

    }

    public function testReplyComment()
    {
        $comment_service = service('comment');

        $dst_type = 1;
        $dst_id = 20;
        $content = 'e20 comment';

        $comment_service->createComment($dst_type, $dst_id, $content);
        $original_comment = $comment_service
            ->search(['dst_type' => 1, 'dst_id' => 20])
            ->setCountPerPage(2)
            ->setCurrentPage(1)
            ->getItems()[0];

        $comment_service->createComment(1, 20, 'reply comment', [
            'reply_uid' => $original_comment->getUser()->uid,
            'reply_id' => $original_comment->id,
            'conv' => $original_comment->id
        ]);

        $new_comments = $comment_service
            ->search(['dst_type' => 1, 'dst_id' => 20])
            ->setCountPerPage(2)
            ->setCurrentPage(1)
            ->getItems();

        foreach ($new_comments as $new_comment) {
            $this->assertEquals($new_comment->conv, $original_comment->id);
            $comment_service->deactivateComment($new_comment->id);
            $comment_service->deleteCommentById($new_comment->id);
        }
    }
}
?>
