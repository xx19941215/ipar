<?php
namespace Ipar\test\TestCase;

class ArticleTagSaveTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/article-tag-save-init.xml');
    }

    public function testSaveTitleNotExist()
    {
        $article_tag_repo = gap_repo_manager()->make('article_tag');
        $pack = $article_tag_repo->save([
            'uid'=>'1',
            'article_id'=>'1',
            'tag_title' => 'title3'
        ]);
        $this->assertTrue($pack->isOk());
        $query_table_tag = $this->getConnection()->createQueryTable(
            'tag',
            'SELECT id, parent_id, locale_id, title, content, child_count, dst_count, vote_total_count, status FROM `tag`'
        );
        $query_table_article_tag = $this->getConnection()->createQueryTable(
            'article_tag',
            'SELECT id, article_id, tag_id, vote_count FROM `article_tag` '
        );
        $query_table_article_tag_vote = $this->getConnection()->createQueryTable(
            'article_tag_vote',
            'SELECT article_tag_id, vote_uid FROM `article_tag_vote`'
        );

        $expected_table_tag = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/article-tag-save-expected2.xml')->getTable('tag');
        $expected_table_article_tag = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/article-tag-save-expected2.xml')->getTable('article_tag');
        $expected_table_article_tag_vote = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/article-tag-save-expected2.xml')->getTable('article_tag_vote');

        $this->assertTablesEqual($expected_table_tag, $query_table_tag);
        $this->assertTablesEqual($expected_table_article_tag, $query_table_article_tag);
        $this->assertTablesEqual($expected_table_article_tag_vote, $query_table_article_tag_vote);
    }


    public function testSaveTitleAleadyExist()
    {
        $article_tag_repo = gap_repo_manager()->make('article_tag');
        $pack = $article_tag_repo->save([
            'uid' => '1',
            'article_id'=>'1',
            'tag_title' => 'title2'
        ]);
        $this->assertTrue($pack->isOk());
        $query_table_tag = $this->getConnection()->createQueryTable(
            'tag',
            'SELECT id, parent_id, locale_id, title, content, child_count, dst_count, vote_total_count, status FROM `tag`'
        );
        $query_table_article_tag = $this->getConnection()->createQueryTable(
            'article_tag',
            'SELECT id, article_id, tag_id, vote_count FROM `article_tag` '
        );
        $query_table_article_tag_vote = $this->getConnection()->createQueryTable(
            'article_tag_vote',
            'SELECT article_tag_id, vote_uid FROM `article_tag_vote`'
        );

        $expected_table_tag = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/article-tag-save-expected.xml')->getTable('tag');
        $expected_table_article_tag = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/article-tag-save-expected.xml')->getTable('article_tag');
        $expected_table_article_tag_vote = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/article-tag-save-expected.xml')->getTable('article_tag_vote');

        $this->assertTablesEqual($expected_table_tag, $query_table_tag);
        $this->assertTablesEqual($expected_table_article_tag, $query_table_article_tag);
        $this->assertTablesEqual($expected_table_article_tag_vote, $query_table_article_tag_vote);
    }

    public function testSaveVoteAlreadyExist()
    {
        $article_tag_repo = gap_repo_manager()->make('article_tag');
        $pack = $article_tag_repo->save([
            'uid' => '1',
            'article_id' => '1',
            'tag_title' => 'title1'
        ]);
        $this->assertEquals('already-exists', $pack->getError('vote'));
    }


}