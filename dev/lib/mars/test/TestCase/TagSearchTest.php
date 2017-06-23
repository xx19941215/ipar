<?php
namespace Mars\Test\TestCase;

class TagSearchTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/tag-search-init.xml');
    }

    public function testSearch()
    {
        $tag_repo = gap_repo_manager()->make('tag');
        $tag_set = $tag_repo->search([
            'search' => 'title'
        ]);

        $this->assertEquals(5, $tag_set->getItemCount());

        $tags = $tag_set->getItems();
        $tag = $tags[0];

        $this->assertInstanceOf('Mars\Dto\TagDto', $tag);
    }
}
