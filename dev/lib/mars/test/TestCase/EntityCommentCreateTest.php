<?php
namespace Mars\Test\TestCase;

class EntityCommentCreateTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/entity-comment-create-init.xml');
    }

    public function testCreate()
    {

        $data = [];
        $data['uid'] = 1;
        $data['entity_type_id'] = 1;
        $data['eid'] = 1;
        $data['content'] = 'this is content';
        $data['opts']['reply_id'] = 1;
        $data['opts']['reply_uid'] = 2;
        $data['opts']['conv'] = 1;


        $pack = gap_repo_manager()->make('entity_comment')->createComment($data);

        $this->assertTrue($pack->isOk());
        $query_table = $this->getConnection()->createQueryTable(
            'entity_comment',
            'SELECT uid,entity_type_id,eid,reply_uid,reply_id,conv,content,status FROM `entity_comment`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/entity-comment-create-expected.xml')->getTable('entity_comment');

        $this->assertTablesEqual($expected_table, $query_table);
    }

}