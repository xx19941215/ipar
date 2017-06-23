<?php
namespace Mars\Test\TestCase;

class GroupCreateTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-create-init.xml');
    }

    public function testCreate()
    {

        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->create([
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'groupTwo-fullname',
            'content' => 'group-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'uid' => 14,
            'locale_id' => 0
        ]);
        $this->assertTrue($pack->isOk());
        $query_table = $this->getConnection()->createQueryTable(
            'group',
            'SELECT gid, uid, type_id, name, fullname, content, logo, website, size_range_id, status, established, type_id FROM `group`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) .
            '/data/group-create-expected.xml')->getTable('group');

        $this->assertTablesEqual($expected_table, $query_table);
    }

    public function testErrorAlreadyExist()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->create([
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'groupOne-fullname',
            'content' => 'group-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'uid' => 14,
            'locale_id' => 0
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('already-exists', $pack->getError('fullname'));
    }

    public function testErrorLengthShort()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->create([
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'g',
            'content' => 'group-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'uid' => 14,
            'locale_id' => 0
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('fullname'));
    }

    public function testErrorLengthLong()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->create([
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'content' => 'group-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'uid' => 14,
            'locale_id' => 0
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('fullname'));
    }

    public function testErrorEmpty()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->create([
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => '',
            'content' => 'group-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'uid' => 14,
            'locale_id' => 0
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('fullname'));
    }

    /*
    public function testErrorEstablished()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->create([
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'group-fullname',
            'content' => 'group-content',
            'established' => 'group',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'uid' => 14,
            'locale_id' => 0
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-date', $pack->getError('established'));
    }

    public function testErrorContent()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->create([
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'group-fullname',
            'content' => '',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'uid' => 14,
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('content'));
    }
    */

    public function testErrorType()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->create([
            'type_id' => 0,
            'name' => 'group-name',
            'fullname' => 'groupTwo-fullname',
            'content' => 'group-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'locale_id' => 0,
            'uid' => 14,
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('type-error', $pack->getError('type'));
    }

    public function testErrorUid()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->create([
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'groupTwo-fullname',
            'content' => 'group-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'locale_id' => 0,
            'uid' => 0,
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('uid'));
    }

    public function testErrorWebsite()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->create([
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'groupTwo-fullname',
            'content' => 'group-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http:www.ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'locale_id' => 0
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('website'));
    }

    public function testErrorContentLength()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->create([
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'groupTwo-fullname',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'uid' => 14,
            'locale_id' => 0,
            'content' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals(['out-of-range-%d', 1000], $pack->getError('content'));
    }
}
