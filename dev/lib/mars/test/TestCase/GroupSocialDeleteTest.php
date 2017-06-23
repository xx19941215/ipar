<?php
namespace Mars\Test\TestCase;

class GroupSocialDeleteTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-social-delete-init.xml');
    }

    public function testCreate()
    {

        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->delete(['id' => 1]);
        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'group_social',
            'SELECT id, gid, social_id, name, url, qrcode FROM `group_social`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-social-delete-expected.xml')
            ->getTable('group_social');
        $this->assertTablesEqual($expected_table, $query_table);
    }
}
