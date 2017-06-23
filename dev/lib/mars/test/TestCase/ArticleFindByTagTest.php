<?php
namespace Mars\Test\TestCase;

class ArticleFindByTagTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/article-find-by-tag-init.xml');
    }

    public function testFind()
    {
        $tag_id = 1;
        $article_set = gap_repo_manager()->make('tag_article')->search(['tag_id' => $tag_id]);
        $this->assertEquals(3, count($article_set->getItems()));

        $tag_id = 2;
        $article_set = gap_repo_manager()->make('tag_article')->search(['tag_id' => $tag_id]);
        $this->assertEquals(2, count($article_set->getItems()));
    }
}