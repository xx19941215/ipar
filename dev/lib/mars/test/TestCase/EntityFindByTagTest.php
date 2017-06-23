<?php
namespace Mars\Test\TestCase;

class EntityFindByTagTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/entity-find-by-tag-init.xml');
    }

    public function testFind()
    {

        $tag_id = 1;
        $entity_type_id = 1;
        $entity_set = gap_repo_manager()->make('tag_entity')->search(['tag_id'=>$tag_id,'entity_type_id'=>$entity_type_id]);
        $this->assertEquals(1, count($entity_set->getItems()));

        $tag_id = 1;
        $entity_type_id = 3;
        $entity_set = gap_repo_manager()->make('tag_entity')->search(['tag_id'=>$tag_id,'entity_type_id'=>$entity_type_id]);
        $this->assertEquals(2, count($entity_set->getItems()));
    }
}