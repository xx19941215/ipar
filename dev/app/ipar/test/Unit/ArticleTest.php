<?php
namespace Ipar\Test\Unit;

class ArticleTest extends \PHPUnit_Framework_TestCase
{
    /**
    *   @dataProvider dummyArticleProvider
    */
    public function testCreateArticle($title, $content, $tag_ids, $locale)
    {

        $article_service = service('article');

        $count = $article_service->getArticleSet()->getItemCount();
        $pack = $article_service->createArticle($title, $content, $locale);
        $new_count = $article_service->getArticleSet()->getItemCount();

        $this->assertTrue($pack->isOk());
        $this->assertEquals(1, intval($new_count) - intval($count));

        $articles = $article_service->getArticleSetAll()->getItems();
        $new_article = array_pop($articles);

        $pack = $article_service->deactivate($new_article->id);
        $new_article = $article_service->getArticleById($new_article->id);
        $this->assertInstanceOf('Ipar\Dto\ArticleDto', $new_article);

        $this->assertTrue($pack->isOk());
        $this->assertEquals(0, $new_article->status);

        $pack = $article_service->deleteArticle($new_article->id);

        $this->assertTrue($pack->isOk());

        return;
    }

    /**
    *   @dataProvider dummyLocaleArticleProvider
    */
    public function testCreateArticleWithLocale($title, $content, $tag_ids, $locale, $original_id)
    {

        $article_service = service('article');

        $result = $article_service->createArticle($title, $content, $locale, $original_id, $tag_ids);
        $this->assertEquals(1, $result->ok);

        $articles = $article_service->getArticleSetAll()->getItems();
        $new_article = array_pop($articles);

        $pack = $article_service->deactivate($new_article->id);
        $new_article = $article_service->getArticleById($new_article->id);

        $this->assertTrue($pack->isOk());
        $this->assertEquals(0, $new_article->status);

        $pack = $article_service->deleteArticle($new_article->id);

        $this->assertTrue($pack->isOk());

        return;
    }

    public function testEditArticle()
    {
        $article_service = service('article');

        $uid = 1;
        $title = 'title1';
        $content = 'content1';
        $tag_ids = [1, 2];
        $locale = 1;

        $pack = $article_service->createArticle($title, $content, $locale, null, $tag_ids);
        $this->assertTrue($pack->isOk());

        $articles = $article_service->getArticleSetAll()->getItems();
        $new_article = array_pop($articles);

        $opts = ['content' => 'edited-content'];
        $id = $new_article->id;

        $pack = $article_service->editArticle($id, $opts);
        $this->assertTrue($pack->isOk());

        $new_article = $article_service->getArticleById($new_article->id);
        $this->assertEquals('edited-content', $new_article->content);

        $pack = $article_service->deactivate($new_article->id);
        $this->assertTrue($pack->isOk());
        $new_article = $article_service->getArticleById($new_article->id);
        $this->assertEquals(0, $new_article->status);

        $pack = $article_service->deleteArticle($new_article->id);
        $this->assertTrue($pack->isOk());

        return;
    }



    public function dummyArticleProvider()
    {
        return [
            ['title' => 'title1', 'content' => 'content1', 'tag_ids' => [1, 2, 3], 'locale'=>'1'],
            ['title' => 'title2', 'content' => 'content2', 'tag_ids' => [2, 3, 4], 'locale'=>'1'],
            ['title' => 'title3', 'content' => 'content3', 'tag_ids' => [5], 'locale'=>'2'],
            ['title' => 'title4', 'content' => 'content4', 'tag_ids' => [2], 'locale'=>'2'],
            ['title' => 'title5', 'content' => 'content5', 'tag_ids' => [3, 1], 'locale'=>'2'],
            ['title' => 'title6', 'content' => 'content6', 'tag_ids' => [4], 'locale'=>'2'],
            ['title' => 'title7', 'content' => 'content7', 'tag_ids' => [3, 2, 1, 5], 'locale'=>'1']
        ];
    }

    public function dummyLocaleArticleProvider()
    {
        return [
            ['title' => 'fr: title', 'content' => 'fr: content', 'tag_ids' => [1, 2, 3], 'locale'=>'4', 'original_id'=>'1'],
        ];
    }

}
