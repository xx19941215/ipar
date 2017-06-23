<?php
namespace Ipar\Test\TestCase;

class AricleTagSearchTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/article-tag-search-init.xml');
    }
    public function testSearch()
    {
        $article_tag_repo = gap_repo_manager()->make('article_tag');
        $tag_set = $article_tag_repo->search([
            'article_id' => '1',
            'query' => 'title1'
        ]);
        $this->assertEquals(1, $this->count($tag_set->getItems()));
        $tag_set = $article_tag_repo->search([
            'article_id' => '1',
            'query' => 'title'
        ]);
        $this->assertEquals(2, count($tag_set->getItems()));
    }
}