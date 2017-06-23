<?php
namespace Mars\Test\Unit;

class FollowTest extends \PHPUnit_Framework_TestCase
{
    public function testFollowEntity()
    {
        $user_service = service('user');
        $entity_follow_service = service('entity_follow');
        $rqt_service = service('rqt');
        $db = db_manager()->get('default');

        $rqt = $rqt_service->createRqt("create new rqt entity to test");
        $eid = $rqt->items['eid'];

        $testers = config()->get('tester.available')->all();
        $count = 0;
        foreach ($testers as $tester) {
            $user_service->switchUserByEmail($tester['email']);
            $current_uid = $user_service->getCurrentUid();
            $followed = $entity_follow_service->follow($eid);
            $count++;
            $this->assertEquals(1, $followed->ok);
            $this->assertEquals(0, $followed->items);
            $this->assertEquals(0, $followed->errors);
        }

        $after_follow = $db->select()
            ->from('entity_analysis')
            ->where('eid', '=', $eid, 'int')
            ->fetchOne();

        $after_follow = $after_follow ? $after_follow->followed_count : 0;
        $this->assertEquals($count, $after_follow);

        foreach ($testers as $tester) {
            $user_service->switchUserByEmail($tester['email']);
            $current_uid = $user_service->getCurrentUid();
            $followed = $entity_follow_service->follow($eid);
            $this->assertEquals(0, $followed->ok);
            $this->assertArrayHasKey('eid', $followed->errors);
        }
        $second_follow = $db->select()
            ->from('entity_analysis')
            ->where('eid', '=', $eid, 'int')
            ->fetchOne();
        $second_follow = $second_follow ? $second_follow->followed_count : 0;
        $this->assertEquals($count, $second_follow);
        foreach ($testers as $tester) {
            $user_service->switchUserByEmail($tester['email']);
            $current_uid = $user_service->getCurrentUid();
            $followed = $entity_follow_service->unfollow($eid);

            $this->assertEquals(1, $followed->ok);
            $this->assertEquals(0, $followed->items);
            $this->assertEquals(0, $followed->errors);
        }
        $after_unfollow = $db->select()
            ->from('entity_analysis')
            ->where('eid', '=', $eid, 'int')
            ->fetchOne();
        $after_unfollow = $after_unfollow ? $after_unfollow->followed_count : 0;
        $this->assertEquals(0, $after_unfollow);

        $rqt_service->deleteRqt($eid);
    }



    public function testFollowUser()
    {
        $user_service = service('user');
        $user_follow_service = service('user_follow');

        $testers = config()->get('tester.available')->all();

        $crt_user = $user_service->switchUserByEmail($testers['tester1']['email']);
        $dst_uid = $user_service->getCurrentUid();
        $db = db_manager()->get('default');

        $before_follow = $db->select()
            ->from('user_analysis')
            ->where('uid', '=', $dst_uid, 'int')
            ->fetchOne();
        $before_follow = $before_follow ? (int)$before_follow->followed_count : 0;

        $count = 0;
        foreach ($testers as $tester) {
            $user_service->switchUserByEmail($tester['email']);
            $current_uid = $user_service->getCurrentUid();
            $followed = $user_follow_service->follow($dst_uid);
            // var_dump($followed);
            $count++;
            $this->assertEquals(1, $followed->ok);
            $this->assertEquals(0, $followed->items);
            $this->assertEquals(0, $followed->errors);
        }

        $after_follow = $db->select()
            ->from('user_analysis')
            ->where('uid', '=', $dst_uid, 'int')
            ->fetchOne();

        $after_follow = $after_follow ? (int)$after_follow->followed_count : 0;
        $this->assertEquals($count, $after_follow - $before_follow);

        $count = 0;
        foreach ($testers as $tester) {
            $user_service->switchUserByEmail($tester['email']);
            $current_uid = $user_service->getCurrentUid();
            $followed = $user_follow_service->follow($dst_uid);
            $count++;
            $this->assertEquals(0, $followed->ok);
            $this->assertArrayHasKey('uid', $followed->errors);
        }

        $second_follow = $db->select()
            ->from('user_analysis')
            ->where('uid', '=', $dst_uid, 'int')
            ->fetchOne();
        $second_follow = $second_follow ? (int)$second_follow->followed_count : 0;
        $this->assertEquals(0, $second_follow - $after_follow);

        foreach ($testers as $tester) {
            $user_service->switchUserByEmail($tester['email']);
            $current_uid = $user_service->getCurrentUid();
            $followed = $user_follow_service->unfollow($dst_uid);
            $this->assertEquals(1, $followed->ok);
            $this->assertEquals(0, $followed->items);
            $this->assertEquals(0, $followed->errors);
        }
        $after_unfollow = $db->select()
            ->from('user_analysis')
            ->where('uid', '=', $dst_uid, 'int')
            ->fetchOne();
        $after_unfollow = $after_unfollow ? (int)$after_unfollow->followed_count : 0;
        $this->assertEquals($before_follow, $after_unfollow);

        $rand_uid = rand(500, 800);
        $rand_follow = $user_follow_service->follow($rand_uid);
        $this->assertEquals(0, $rand_follow->ok);
        $this->assertArrayHasKey('uid', $rand_follow->errors);
    }
}
