<?php
namespace Mars\Test\TestCase;

class EntityTagSaveTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/entity-tag-save-init.xml');
    }


    public function testSaveTitleNotExist()
    {
        $entity_tag_repo = gap_repo_manager()->make('entity_tag');
        $pack = $entity_tag_repo->save([
            'uid' => '1',
            'eid' => '1',
            'entity_type_id' => '1',
            'tag_title' => 'title3'
        ]);
        $this->assertTrue($pack->isOk());

        $query_table_tag = $this->getConnection()->createQueryTable(
            'tag',
            'SELECT id, parent_id, locale_id, title, content, child_count, dst_count, vote_total_count, status FROM `tag`'
        );
        $query_table_entity_tag = $this->getConnection()->createQueryTable(
            'entity_tag',
            'SELECT id, entity_type_id, eid, tag_id, vote_count FROM `entity_tag` '
        );
        $query_table_entity_tag_vote = $this->getConnection()->createQueryTable(
            'entity_tag_vote',
            'SELECT entity_tag_id, vote_uid FROM `entity_tag_vote`'
        );

        $expected_table_tag = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/entity-tag-save-expected2.xml')->getTable('tag');
        $expected_table_entity_tag = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/entity-tag-save-expected2.xml')->getTable('entity_tag');
        $expected_table_entity_tag_vote = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/entity-tag-save-expected2.xml')->getTable('entity_tag_vote');

        $this->assertTablesEqual($expected_table_tag, $query_table_tag);
        $this->assertTablesEqual($expected_table_entity_tag, $query_table_entity_tag);
        $this->assertTablesEqual($expected_table_entity_tag_vote, $query_table_entity_tag_vote);
    }



    public function testSaveTitleAleadyExist()
    {
        $entity_tag_repo = gap_repo_manager()->make('entity_tag');
        $pack = $entity_tag_repo->save([
            'uid' => '1',
            'eid' => '1',
            'entity_type_id' => '1',
            'tag_title' => 'title2'
        ]);
        $this->assertTrue($pack->isOk());

        $query_table_tag = $this->getConnection()->createQueryTable(
            'tag',
            'SELECT id, parent_id, locale_id, title, content, child_count, dst_count, vote_total_count, status FROM `tag`'
        );
        $query_table_entity_tag = $this->getConnection()->createQueryTable(
            'entity_tag',
            'SELECT id, entity_type_id, eid, tag_id, vote_count FROM `entity_tag` '
        );
        $query_table_entity_tag_vote = $this->getConnection()->createQueryTable(
            'entity_tag_vote',
            'SELECT entity_tag_id, vote_uid FROM `entity_tag_vote`'
        );

        $expected_table_tag = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/entity-tag-save-expected.xml')->getTable('tag');
        $expected_table_entity_tag = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/entity-tag-save-expected.xml')->getTable('entity_tag');
        $expected_table_entity_tag_vote = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/entity-tag-save-expected.xml')->getTable('entity_tag_vote');

        $this->assertTablesEqual($expected_table_tag, $query_table_tag);
        $this->assertTablesEqual($expected_table_entity_tag, $query_table_entity_tag);
        $this->assertTablesEqual($expected_table_entity_tag_vote, $query_table_entity_tag_vote);
    }

    public function testSaveVoteAlreadyExist()
    {
        $entity_tag_repo = gap_repo_manager()->make('entity_tag');
        $pack = $entity_tag_repo->save([
            'uid' => '1',
            'eid' => '1',
            'entity_type_id' => '1',
            'tag_title' => 'title1'
        ]);
        $this->assertEquals('already-exists', $pack->getError('vote'));
    }

}