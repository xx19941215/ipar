<?php
namespace Mars\Test\TestCase;

class EntityCommentDeleteTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/entity-comment-delete-init.xml');
    }

    public function testDelete()
    {
        $pack = gap_repo_manager()->make('entity_comment')->deleteCommentById(1);
        $this->assertTrue($pack->isOk());
    }

}