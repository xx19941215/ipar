<?php
namespace Mars\Test\TestCase;

class GroupUpdateTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-update-init.xml');
    }

    public function testUpdate()
    {
        $group_repo = gap_repo_manager()->make('group');

        $pack = $group_repo->update(['gid' => 1], [
            'type_id' => get_type_id('group'),
            'name' => 'group-update-name',
            'fullname' => 'group-update-fullname',
            'content' => 'group-update-content',
            'established' => '2016-08-01 10:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 2,
            'uid' => 14,
        ]);

        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'group',
            'SELECT gid,uid,type_id,name,fullname,content,logo,website,size_range_id,status,established FROM`group`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) .
            '/data/group-update-expected.xml')->getTable('group');

        $this->assertTablesEqual($expected_table, $query_table);
    }

    public function testErrorAlreadyExist()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->update(['gid' => 1], [
            'type_id' => get_type_id('group'),
            'name' => 'group-update-name',
            'fullname' => 'group-fullname',
            'content' => 'group-update-content',
            'established' => '2016-08-01 10:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 2,
            'uid' => 14,
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('already-exists', $pack->getError('fullname'));
    }

    public function testErrorLengthShort()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->update(['gid' => 1], [
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'g',
            'content' => 'group-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'locale_id' => 0,
            'uid' => 14,

        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('fullname'));
    }

    public function testErrorLengthLong()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->update(['gid' => 1], [
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'content' => 'group-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'locale_id' => 0,
            'uid' => 14,
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('fullname'));
    }

    public function testErrorEmpty()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->update(['gid' => 1], [
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => '',
            'content' => 'group-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'locale_id' => 0,
            'uid' => 14,
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('fullname'));
    }

    /*
    public function testErrorEstablished()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->update(['gid' => 1], [
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'group-update-fullname',
            'content' => 'group-content',
            'established' => 'group',
            'logo' => '',
            'website' => '',
            'size_range_id' => 1,
            'locale_id' => 0,
            'uid' => 14,
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-date', $pack->getError('established'));
    }

    public function testErrorContent()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->update(['gid' => 1], [
            'type_id' => get_type_id('group'),
            'name' => 'group-name',
            'fullname' => 'group-update-fullname',
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
        $pack = $group_repo->update(['gid' => 1], [
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

    public function testUpdateField()
    {
        $this->getDataSet();
        $group_repo = gap_repo_manager()->make('group');

        $status = 2;
        $pack = $group_repo->updateField(['gid' => 1], 'status', $status);

        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'group',
            'SELECT gid,uid,type_id,name,fullname,content,logo,website,size_range_id,status,established FROM`group`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) .
            '/data/group-update-field-expected.xml')->getTable('group');

        $this->assertTablesEqual($expected_table, $query_table);
    }

    public function testErrorWebsite()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->update(['gid' => 1], [
            'type_id' => get_type_id('group'),
            'name' => 'group-update-name',
            'fullname' => 'group-update-fullname',
            'content' => 'group-update-content',
            'established' => '2016-08-01 10:00:00',
            'logo' => '',
            'website' => 'http:www.baidu.com',
            'size_range_id' => 2,
            'uid' => 14,
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('website'));
    }

    public function testErrorContentLength()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->update(['gid' => 1], [
            'type_id' => get_type_id('group'),
            'name' => 'group-update-name',
            'fullname' => 'group-update-fullname',
            'established' => '2016-08-01 10:00:00',
            'logo' => '',
            'website' => '',
            'size_range_id' => 2,
            'uid' => 14,
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
