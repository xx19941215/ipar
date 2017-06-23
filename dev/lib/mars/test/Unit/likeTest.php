<?php
namespace Mars\Test\Unit;

class LikeTest extends \PHPUnit_Framework_TestCase
{
    public function testlike()
    {
        $like_service = service('entity_like');
        $rqt_service = service('rqt');
        $user_service = service('user');
        $db = db_manager()->get('default');

        $rqt = $rqt_service->createRqt("create new rqt entity for entity like test");
        $eid = $rqt->items['eid'];

        $testers = config()->get('tester.available')->all();

        // test entity like
        $count = 0;
        foreach ($testers as $tester) {
            $user_service->switchUserByEmail($tester['email']);
            $current_uid = $user_service->getCurrentUid();
            $liked = $like_service->like($eid);
            $count++;
            $this->assertEquals(1, $liked->ok);
            $this->assertEquals(0, $liked->items);
            $this->assertEquals(0, $liked->errors);
        }

        // test like the liked entity
        $liked = $like_service->like($eid);
        $this->assertEquals(0, $liked->ok);

        $after_like = $db->select()
            ->from('entity_analysis')
            ->where('eid', '=', $eid, 'int')
            ->fetchOne();
        $after_like = $after_like ? $after_like->like_count : 0;
        $this->assertEquals($count, $after_like);

        // test entity unlike
        $count = 0;
        foreach ($testers as $tester) {
            $user_service->switchUserByEmail($tester['email']);
            $current_uid = $user_service->getCurrentUid();
            $liked = $like_service->unlike($eid);
            $count++;
            $this->assertEquals(1, $liked->ok);
            $this->assertEquals(0, $liked->items);
            $this->assertEquals(0, $liked->errors);
        }
        $after_unlike = $db->select()
            ->from('entity_analysis')
            ->where('eid', '=', $eid, 'int')
            ->fetchOne();
        $after_unlike = $after_unlike ? $after_unlike->like_count : 0;
        $this->assertEquals($count+$after_unlike, $after_like);

        // test unlike the not exists entity
        $unliked = $like_service->unlike($eid);
        $this->assertEquals(0, $unliked->ok);

        $rqt_service->deleteRqt($eid);
    }

    // public function testDataSet()
    // {
    //     $like_service = service('entity_like');
    //     $data = $like_service->schEntityLikeSet(['eid' => 776]);

    //     var_dump($data->getItems());
    // }
}
