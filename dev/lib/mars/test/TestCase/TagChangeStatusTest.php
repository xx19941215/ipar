<?php
namespace Mars\Test\TestCase;

class TagChangeStatusTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/tag-change-status-init.xml');
    }

    public function testChangeStatus()
    {
        $tag_repo = gap_repo_manager()->make('tag');
        $tag_id = 1;
        $status = 0;
        $pack = $tag_repo->updateField(['id'=>$tag_id], 'status',$status);
        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'tag',
            'SELECT id, parent_id, locale_id, title, content, child_count, dst_count, vote_total_count, status FROM `tag`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/tag-change-status-expected.xml')->getTable('tag');

        $this->assertTablesEqual($expected_table, $query_table);
    }
}