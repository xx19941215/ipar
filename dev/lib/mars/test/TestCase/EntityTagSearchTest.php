<?php
namespace Mars\Test\TestCase;

class EntityTagSearchTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/entity-tag-search-init.xml');
    }
    public function testSearch()
    {
        $entity_tag_repo = gap_repo_manager()->make('entity_tag');
        $tag_set = $entity_tag_repo->search([
            'eid' => '1',
            'entity_type_id' => '1',
            'query' => 'title1'
        ]);
        $this->assertEquals(1, $this->count($tag_set->getItems()));
        $tag_set = $entity_tag_repo->search([
            'eid' => '1',
            'entity_type_id' => '1',
            'query' => 'title'
        ]);
        $this->assertEquals(2, count($tag_set->getItems()));
    }

}