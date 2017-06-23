<?php
namespace Mars\Test\TestCase;

class IndustryTagDeleteTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/industry_tag-delete-init.xml');
    }

    public function testDelete()
    {
        $industry_tag_repo = gap_repo_manager()->make('industry_tag');
        $pack = $industry_tag_repo->delete(['tag_id' => 2]);
        $this->assertTrue($pack->isOk());

        if ($pack->isOk()) {
            $query_table = $this->getConnection()->createQueryTable(
                'tag',
                'SELECT id, parent_id, locale_id, title, content, child_count, dst_count, vote_total_count, status FROM `tag`'
            );
            $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/industry_tag-delete-expected.xml')->getTable('tag');

            $this->assertTablesEqual($expected_table, $query_table);

            $query_table = $this->getConnection()->createQueryTable(
                'industry_tag',
                'SELECT tag_id FROM `industry_tag`'
            );
            $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/industry_tag-delete-expected.xml')->getTable('industry_tag');

            $this->assertTablesEqual($expected_table, $query_table);
        }

    }
}