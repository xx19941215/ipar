<?php
namespace Mars\Test\TestCase;

class GroupContactUpdateTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-contact-update-init.xml');
    }

    public function testUpdate()
    {

        $group_contact_repo = gap_repo_manager()->make('group_contact');
        $pack = $group_contact_repo->update(['gid' => 1], [
            'gid' => 1,
            'name' => 'karan',
            'roles' => 'secret',
            'phone' => '123456789',
            'email' => '123456789@126.com',
            'contact_uid' => 983
        ]);
        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'group_contact',
            'SELECT gid, name, roles, phone, email, contact_uid FROM `group_contact`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-contact-update-expected.xml')
            ->getTable('group_contact');
        $this->assertTablesEqual($expected_table, $query_table);
    }


    public function testErrorLengthShort()
    {
        $group_contact_repo = gap_repo_manager()->make('group_contact');
        $pack = $group_contact_repo->update(['gid' => 1], [
            'gid' => 1,
            'name' => 'k',
            'roles' => 'manager',
            'phone' => '123456789',
            'email' => '123456789@126.com',
            'contact_uid' => 983
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('name'));
    }

    public function testErrorLengthLong()
    {
        $group_contact_repo = gap_repo_manager()->make('group_contact');
        $pack = $group_contact_repo->update(['gid' => 1], [
            'gid' => 1,
            'name' => 'kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk
                kkkkkkkkkkkkkkkkkkkkkkk',
            'roles' => 'manager',
            'phone' => '123456789',
            'email' => '123456789@126.com',
            'contact_uid' => 983
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('name'));
    }

    public function testErrorEmptyName()
    {
        $group_contact_repo = gap_repo_manager()->make('group_contact');
        $pack = $group_contact_repo->update(['gid' => 1], [
            'gid' => 1,
            'name' => '',
            'roles' => 'manager',
            'phone' => '123456789',
            'email' => '123456789@126.com',
            'contact_uid' => 983
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('name'));
    }

    public function testErrorPhone()
    {
        $group_contact_repo = gap_repo_manager()->make('group_contact');
        $pack = $group_contact_repo->update(['gid' => 1], [
            'gid' => 1,
            'name' => 'karan',
            'roles' => 'manager',
            'phone' => '',
            'email' => '123456789@126.com',
            'contact_uid' => 983
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('phone'));
    }

    public function testErrorEmail()
    {
        $group_contact_repo = gap_repo_manager()->make('group_contact');
        $pack = $group_contact_repo->update(['gid' => 1], [
            'gid' => 1,
            'name' => 'karan',
            'roles' => 'manager',
            'phone' => '123456789',
            'email' => '',
            'contact_uid' => 983
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('email'));
    }

    public function testErrorGroupid()
    {
        $group_contact_repo = gap_repo_manager()->make('group_contact');
        $pack = $group_contact_repo->update(['gid' => 1], [
            'gid' => 'k',
            'name' => 'karan',
            'roles' => 'manager',
            'phone' => 123456789,
            'email' => '123456789@126.com',
            'contact_uid' => 983
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('gid'));
    }
}
