<?php
namespace Mars\Test\TestCase;

Class GroupDeleteTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-delete-init.xml');
    }


    public function testDelete()
    {
        $group_repo = gap_repo_manager()->make('group');
        $pack = $group_repo->delete(['gid' => 2]);

        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'group',
            'SELECT gid,uid,type_id,name,fullname,content,logo,website,size_range_id,status,established FROM`group`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-delete-expected.xml')->getTable('group');

        $this->assertTablesEqual($expected_table, $query_table);
    }
}