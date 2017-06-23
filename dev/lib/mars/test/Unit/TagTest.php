<?php
namespace Mars\Test\Unit;

class TagTest extends \PHPUnit_Framework_TestCase
{
    protected static $dst_obj;
    protected static $dst_type;
    protected static $dst_id;

    protected static $tag_dst_vote_set;

    protected static $titles = [
            'C10K', 'Google', '编程', 'DevOps', '负载均衡',
            'Database', 'MySQL', 'MariaDB', '广告', 'Nginx',
            '代码质量', '互联网', 'PHP', 'Java', 'NodeJS',
            '集群', 'Code Review', '代码审查', '测试', 'Testing'
        ];

    public static function setUpBeforeClass()
    {
        $pack = service('rqt')->createRqt('create new rqt to test tagEntity');

        $eid = $pack->getItem('eid');

        self::$dst_type = get_type_id('rqt');
        self::$dst_id = $eid;
        self::$dst_obj = service('rqt')->getEntityByEid($eid);

        self::$tag_dst_vote_set = [];
    }

    public static function tearDownAfterClass()
    {
        $pack = service('tag')->deleteTagDst([
                'dst_type' => self::$dst_type,
                'dst_id' => self::$dst_id
            ]);

        foreach (self::$titles as $title) {
            $pack = service('tag_admin')->deleteTag(['title' => $title]);
        }

        $pack = service('rqt')->deleteRqtByEid(self::$dst_id);
    }

    public function provider()
    {
        $testers = config()->get('tester.available')->all();

        $titles = self::$titles;

        $max = count($titles) - 1;

        for ($i = 0; $i <= 5; $i++) {
            foreach ($testers as $tester) {
                $data[] = [$tester['email'], $titles[rand(0, $max)]];
            }
        }
        return $data;
    }

    public function testSaveTagDstError()
    {
        $dst_type = self::$dst_type;
        $dst_id = self::$dst_id;

        $tag_service = service('tag');

        $pack = $tag_service->saveTagDst("fsda", "fdsa", 'hello');
        $this->assertEquals('not-positive', $pack->getError('dst_type'));

        $pack = $tag_service->saveTagDst($dst_type, 'fdsafsa', 'hello');
        $this->assertEquals('not-positive', $pack->getError('dst_id'));

        $pack = $tag_service->saveTagDst($dst_type, $dst_id, '');
        $this->assertEquals('empty', $pack->getError('title'));
    }

    /**
     * @dataProvider provider
     */
    public function testSaveTagDst($email, $title)
    {
        $dst_type = self::$dst_type;
        $dst_id = self::$dst_id;
        $tag_service = service('tag');
        $user_service = service('user');

        $user_service->switchUserByEmail($email);
        $current_uid = $user_service->getCurrentUid();

        $pack = $tag_service->saveTagDst($dst_type, $dst_id, $title);
        if ($pack->isOk()) {
            $this->localVoteTagDst($current_uid, $pack->getItem('tag_dst_id'));
            $pack = $tag_service->saveTagDst($dst_type, $dst_id, $title);
        }

        $this->assertFalse($pack->isOk());
        $this->assertEquals('duplicated', $pack->getError('vote'));
    }

    /**
     * @depends testSaveTagDst
     */
    public function testVoteTagDst()
    {
        $tag_dst_set = self::$dst_obj->getTagDstSet();
        $tag_dst_set->setCountPerPage(0);

        $user_service = service('user');
        $tag_service = service('tag');

        $latest_tag_dst = null;
        $current_uid = $user_service->getCurrentUid();

        foreach ($tag_dst_set->getItems() as $tag_dst) {
            if (!$tag_dst->hasVoted()) {
                $pack = $tag_service->voteTagDst($tag_dst->id);
                $this->assertTrue($pack->isOk());
                if ($pack->isOk()) {
                    $this->localVoteTagDst($current_uid, $tag_dst->id);
                }
            }
            $pack = $tag_service->voteTagDst($tag_dst->id);
            $this->assertFalse($pack->isOk());
            $this->assertEquals('duplicated', $pack->getError('vote'));

            $latest_tag_dst = $tag_dst;
        }

        foreach (config()->get('tester.available')->all() as $tester) {
            $user_service->switchUserByEmail($tester['email']);
            if ($latest_tag_dst->hasVoted()) {
                $pack = $tag_service->unvoteTagDst($latest_tag_dst->id);
                $this->assertTrue($pack->isOk());
                $this->localUnvoteTagDst($user_service->getCurrentUid(), $latest_tag_dst->id);
            }

            $pack = $tag_service->unvoteTagDst($latest_tag_dst->id);
            $this->assertFalse($pack->isOk());
            $this->assertEquals('not-voted', $pack->getError('vote'));
        }
    }

    /**
     * @depends testVoteTagDst
     */
    public function testGetTagDstSet()
    {
        $tag_dst_set = self::$dst_obj->getTagDstSet();
        $tag_dst_set->setCountPerPage(0);

        foreach ($tag_dst_set->getItems() as $tag_dst) {
            $count = count(self::$tag_dst_vote_set[$tag_dst->id]);
            $this->assertEquals(
                $count,
                $tag_dst->getVoteCount()
            );

            $vote_set = $tag_dst->getVoteSet();
            $vote_set->setCountPerPage(0);
            $votes = $vote_set->getItems();

            $this->assertEquals($count, count($votes));

            foreach ($votes as $vote) {
                $this->assertTrue(isset(self::$tag_dst_vote_set[$tag_dst->id][$vote->vote_uid]));
                $vote_user = $vote->getVoteUser();
                $this->assertEquals($vote->vote_uid, $vote_user->uid);
            }
        }
    }

    protected function localVoteTagDst($current_uid, $tag_dst_id)
    {
        self::$tag_dst_vote_set[$tag_dst_id][$current_uid] = 1;
    }
    protected function localUnvoteTagDst($current_uid, $tag_dst_id)
    {
        if ($this->localHasVoted($current_uid, $tag_dst_id)) {
            unset(self::$tag_dst_vote_set[$tag_dst_id][$current_uid]);
        }
    }
    protected function localHasVoted($current_uid, $tag_dst_id)
    {
        if (isset(self::$tag_dst_vote_set[$tag_dst_id])
            && is_array(self::$tag_dst_vote_set[$tag_dst_id])
            && isset(self::$tag_dst_vote_set[$tag_dst_id][$current_uid])
            && (1 === self::$tag_dst_vote_set[$tag_dst_id][$current_uid])
        ) {
            return true;
        }

        return false;
    }
}
