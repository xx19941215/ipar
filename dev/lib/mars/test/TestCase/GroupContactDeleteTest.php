<?php
namespace Mars\Test\TestCase;

class GroupContactDeleteTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-contact-delete-init.xml');
    }

    public function testDelete()
    {

        $group_contact_repo = gap_repo_manager()->make('group_contact');
        $pack = $group_contact_repo->delete(['id' => 1]);
        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'group_contact',
            'SELECT gid, name, roles, phone, email, contact_uid FROM `group_contact`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-contact-delete-expected.xml')
            ->getTable('group_contact');
        $this->assertTablesEqual($expected_table, $query_table);
    }
}
