<?php
namespace Mars\Test\TestCase;

class TagUpdateTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/tag-update-init.xml');
    }

    public function testUpdate()
    {
        $tag_repo = gap_repo_manager()->make('tag');
        $query = ['id'=>1];
        $data = ['title' => 'title2', 'content' => 'content2'];
        $pack = $tag_repo->update($query,$data);

        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'tag',
            'SELECT id, parent_id, locale_id, title, content, child_count, dst_count, vote_total_count, status FROM `tag`'
        );
        //$query_table = $this->getConnection()->createQueryTable('group', 'SELECT * FROM `group`');
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/tag-update-expected.xml')->getTable('tag');

        $this->assertTablesEqual($expected_table, $query_table);
    }

    public function testErrorAlreadyExists()
    {
        $tag_repo = gap_repo_manager()->make('tag');
        $pack = $tag_repo->create([
            'parent_id'=>'0',
            'locale_id'=>'0',
            'title'=>'title1',
            'content'=>'content1',
            'status'=>'1'
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('already-exists', $pack->getError('title'));
    }

    public function testErrorNotEmpty()
    {
        $tag_repo = gap_repo_manager()->make('tag');
        $pack = $tag_repo->create([
            'parent_id'=>'0',
            'locale_id'=>'0',
            'title' => '',
            'content' => 'content1',
            'status'=>'1'
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('title'));
    }
}