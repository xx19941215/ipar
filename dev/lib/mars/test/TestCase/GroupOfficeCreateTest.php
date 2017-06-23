<?php
namespace Mars\Test\TestCase;

class GroupOfficeCreateTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-office-create-init.xml');
    }

    public function testCreate()
    {

        $group_office_repo = gap_repo_manager()->make('group_office');
        $pack = $group_office_repo->create([
            'gid' => 1,
            'name' => 'kuipu',
            'office_address' => 'YangPu,shanghai',
        ]);
        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'group_office',
            'SELECT gid, name, office_address FROM `group_office`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-office-create-expected.xml')
            ->getTable('group_office');
        $this->assertTablesEqual($expected_table, $query_table);
    }


    public function testErrorLengthShortName()
    {
        $group_office_repo = gap_repo_manager()->make('group_office');
        $pack = $group_office_repo->create([
            'gid' => 1,
            'name' => 'k',
            'office_address' => '上海市杨浦区335号',
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('name'));
    }

    public function testErrorLengthLongName()
    {
        $group_office_repo = gap_repo_manager()->make('group_office');
        $pack = $group_office_repo->create([
            'gid' => 1,
            'name' => 'kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk
            kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk',
            'office_address' => '上海市杨浦区335号',
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('name'));
    }

    public function testErrorLengthShortAddress()
    {
        $group_office_repo = gap_repo_manager()->make('group_office');
        $pack = $group_office_repo->create([
            'gid' => 1,
            'name' => '总公司',
            'office_address' => 'k',
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('office_address'));
    }

    public function testErrorLengthLongAddress()
    {
        $group_office_repo = gap_repo_manager()->make('group_office');
        $pack = $group_office_repo->create([
            'gid' => 1,
            'name' => '总公司',
            'office_address' => 'kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk
            kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk',
        ]);

        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('office_address'));
    }


    public function testErrorEmptyName()
    {
        $group_office_repo = gap_repo_manager()->make('group_office');
        $pack = $group_office_repo->create([
            'gid' => 1,
            'name' => '',
            'office_address' => '上海市杨浦区335号',
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('name'));
    }

    public function testErrorEmptyAddress()
    {
        $group_office_repo = gap_repo_manager()->make('group_office');
        $pack = $group_office_repo->create([
            'gid' => 1,
            'name' => '总公司',
            'office_address' => '',
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('office_address'));
    }
}
