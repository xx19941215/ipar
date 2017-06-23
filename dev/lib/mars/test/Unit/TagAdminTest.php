<?php
namespace Mars\Test\Unit;

class TagAdminTest extends \PHPUnit_Framework_TestCase
{
    protected static $tag_titles = [
            [['PHP', 'en-us'],],
            [['html', 'en-us'],],
            [['css', 'en-us'],],
            [['Linux', 'en-us']],
            [['Javascript', 'en-us']],
            [['node.js', 'en-us']],

            [['Art', 'en-us'], ['艺术', 'zh-cn'],],
            [['Design', 'en-us'], ['设计', 'zh-cn'],],
            [['Geek', 'en-us'], ['极客', 'zh-cn'],],
            [['Travel', 'en-us'], ['旅游', 'zh-cn'],],
            [['Music', 'en-us'], ['音乐', 'zh-cn'],],
            [['Sports', 'en-us'], ['体育', 'zh-cn']],
            [['Algorithms', 'en-us'], ['算法', 'zh-cn']],
            [['Websites', 'en-us'], ['网站', 'zh-cn']],

            [['Facebook', 'en-us'], ['脸书', 'zh-cn']],
            [['Google', 'en-us'], ['谷歌', 'zh-cn']],
            [['Microsoft', 'en-us'], ['微软', 'zh-cn']],
        ];

    protected static $tag_set = [];

    public static function tearDownAfterClass()
    {
        $tag_admin_service = service('tag_admin');

        foreach (self::$tag_titles as $titles) {
            foreach ($titles as $title) {
                $tag_admin_service->deleteTag(['title' => $title[0]]);
            }
        }
    }

    public function provideTagTitles()
    {
        $tag_titles = [];
        foreach (self::$tag_titles as $titles) {
            $tag_titles[] = $titles[0];
        }
        return $tag_titles;
    }

    /**
     * @dataProvider provideTagTitles
     */
    public function testSaveTag($title, $locale_key)
    {
        $locale_id = locale_set()->getId($locale_key);

        $tag_service = service('tag_admin');
        $pack = service('tag_admin')->saveTag($title, $locale_id);
        $this->assertTrue($pack->isOk());

        $tag_id = $pack->getItem('tag_id');
        $tag = $tag_service->findTag(['tag_id' => $tag_id]);

        $this->assertEquals($title, $tag->getTagTitle($locale_id));

        self::$tag_set[$tag_id][$locale_id] = $title;
    }

    /**
     * @depends testSaveTag
     */
    public function testCreateTagMainError()
    {
        $tag_admin_service = service('tag_admin');

        $pack = $tag_admin_service->createTagMain(
            0,
            0,
            'tag-title-for-test',
            'tag-main-for-test',
            'tag-zcode-for-test'
        );
        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-positive', $pack->getError('tag_id'));

        $pack = $tag_admin_service->createTagMain(1, 0, '', '', '');
        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-positive', $pack->getError('locale'));

        $pack = $tag_admin_service->createTagMain(1, 2, '', '', '');
        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('title'));

        $tag_title = self::$tag_titles[0][0];
        $title = $tag_title[0];
        $locale_id = locale_set()->getId($tag_title[1]);
        $tag_main = $tag_admin_service->findTagMain(['title' => $title]);

        $pack = $tag_admin_service->createTagMain(
            $tag_main->tag_id,
            $tag_main->locale_id,
            uniqid('title-'),
            $tag_main->content,
            uniqid('zcode-')
        );
        $this->assertFalse($pack->isOk());
        $this->assertEquals('duplicated', $pack->getError('tag_id-locale'));

        $not_found_tag_id = 9999999999;
        $pack = $tag_admin_service->createTagMain($not_found_tag_id, 1, 'not exist tag id', '');
        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-found', $pack->getError('tag_id'));

        $pack = $tag_admin_service->createTagMain(
            $tag_main->tag_id,
            $locale_id + 1,
            $tag_main->title,
            $tag_main->content,
            uniqid('zcode-')
        );
        $this->assertFalse($pack->isOk());
        $this->assertEquals('duplicated', $pack->getError('title'));

        $pack = $tag_admin_service->createTagMain(
            $tag_main->tag_id,
            $locale_id + 1,
            uniqid('title-'),
            $tag_main->content,
            $tag_main->zcode
        );
        $this->assertFalse($pack->isOk());
        $this->assertEquals('duplicated', $pack->getError('zcode'));
    }

    /**
     * @depends testCreateTagMainError
     */
    public function testCreateTagMain()
    {
        $tag_admin_service = service('tag_admin');

        foreach (self::$tag_titles as $titles) {
            if (isset($titles[1])) {
                $tag = $tag_admin_service->findTag(['title' => $titles[0][0]]);
                $locale_id = locale_set()->getId($titles[1][1]);
                $title = $titles[1][0];
                $pack = $tag_admin_service->createTagMain($tag->id, $locale_id, $title, '');
                $this->assertTrue($pack->isOk());
                self::$tag_set[$tag->id][$locale_id] = $title;
            }
        }
    }

    /**
     * @depends testCreateTagMain
     */
    public function testUpdateTagMain()
    {
        $tag_admin_service = service('tag_admin');
        $title = '电子';
        $locale_id = locale_set()->getId('zh-cn');

        $pack = $tag_admin_service->saveTag($title, $locale_id);
        $this->assertTrue($pack->isOk());

        $tag_main = $tag_admin_service->findTagMain(['title' => $title]);
        $new_title = '电子设备';

        $pack = $tag_admin_service->updateTagMain($tag_main->tag_id, $tag_main->locale_id, $new_title, '', null);
        $this->assertTrue($pack->isOk());

        $tag = $tag_admin_service->findTag(['tag_id' => $tag_main->tag_id]);
        $this->assertEquals($new_title, $tag->getTagTitle($locale_id));

        $pack = $tag_admin_service->deleteTag(['title' => $title]);
        $pack = $tag_admin_service->deleteTag(['title' => $new_title]);
    }

    /**
     * @depends testUpdateTagMain
     */
    public function testGetTagMainSet()
    {
        $tag_admin_service = service('tag_admin');
        foreach (self::$tag_set as $tag_id => $tag_titles) {
            $tag = $tag_admin_service->findTag(['tag_id' => $tag_id]);
            if (!$tag) {
                continue;
            }
            $tag_main_set = $tag->getTagMainSet();
            $this->assertEquals($tag_main_set->getItemCount(), count($tag_titles));
            $tag_main_set->setCountPerPage(0);
            $tag_main_items = $tag_main_set->getItems();
            foreach ($tag_main_items as $tag_main) {
                $this->assertEquals($tag_titles[$tag_main->locale_id], $tag_main->title);
            }
        }
    }

    /**
     * @depends testGetTagMainSet
     */
    public function testDeleteTagMain()
    {
        $tag_admin_service = service('tag_admin');
        $title = uniqid('China-');
        $locale_id = locale_set()->getId('en-us');

        $pack = $tag_admin_service->saveTag($title, $locale_id);
        $this->assertTrue($pack->isOk());

        $tag = $tag_admin_service->findTag(['tag_id' => $pack->getItem('tag_id')]);
        $tag_id = $tag->id;

        $pack = $tag_admin_service->deleteTagMain(['tag_id' => $tag_id, 'locale_id' => $locale_id]);
        $this->assertTrue($pack->isOk());

        $this->assertEquals(0, $tag->getTagMainSet()->getItemCount());

        $pack = $tag_admin_service->deleteTag(['tag_id' => $tag_id]);
    }

    /**
     * @depends testDeleteTagMain
     */
    public function testSchTagSet()
    {
        $tag_admin_service = service('tag_admin');
        $tag_set = $tag_admin_service->schTagSet();
        foreach ($tag_set->getItems() as $tag) {
            if (isset(self::$tag_set[$tag->id])) {
                $tag_arr = self::$tag_set[$tag->id];
                foreach ($tag_arr as $locale_id => $title) {
                    $this->assertEquals($title, $tag->getTagTitle($locale_id));
                }
            }
        }

        $tag_set = $tag_admin_service->schTagSet(['keywords' => 'Trav']);
        foreach ($tag_set->getItems() as $tag) {
            $tag_arr = self::$tag_set[$tag->id];
            foreach ($tag_arr as $locale_id => $title) {
                $this->assertEquals($title, $tag->getTagTitle($locale_id));
            }
        }
    }

    /**
     * @depends testSchTagSet
     */
    public function testActivateTag()
    {
        $title = uniqid('title-');
        $locale_id = locale_set()->getId('en-us');
        $tag_admin_service = service('tag_admin');

        $pack = $tag_admin_service->saveTag($title, $locale_id);
        $this->assertTrue($pack->isOk());

        $tag_id = $pack->getItem('tag_id');

        $tag = $tag_admin_service->findTag(['title' => $title]);
        $this->assertEquals($tag_id, $tag->id);

        $pack = $tag_admin_service->deactivateTagById($tag_id);

        $tag = $tag_admin_service->findTag(['title' => $title]);
        $this->assertFalse($tag);

        $tag = $tag_admin_service->findTag(['title' => $title, 'status' => null]);
        $this->assertEquals(0, $tag->status);

        $pack = $tag_admin_service->activateTagById($tag_id);
        $this->assertTrue($pack->isOk());

        $tag = $tag_admin_service->findTag(['title' => $title, 'status' => null]);
        $this->assertEquals(1, $tag->status);

        $tag_admin_service->deleteTag(['tag_id' => $tag_id]);
    }
}
