<?php
namespace Mars\Test\TestCase;

class GroupOfficeDeleteTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-office-delete-init.xml');
    }

    public function testDelete()
    {

        $group_office_repo = gap_repo_manager()->make('group_office');
        $pack = $group_office_repo->delete(['id' => 1]);
        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'group_office',
            'SELECT gid, name, office_address FROM `group_office`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-office-delete-expected.xml')
            ->getTable('group_office');
        $this->assertTablesEqual($expected_table, $query_table);
    }
}
