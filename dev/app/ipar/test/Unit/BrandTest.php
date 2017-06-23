<?php
namespace Ipar\Test\Unit;

class BrandTest extends \PHPUnit_Framework_TestCase
{
    protected static $product_eid;

    protected static $brand_titles = [
        'Taobao',
        '淘宝',
        'Nike',
        '耐克',
        'Adidas',
        '阿迪达斯',
        'Sony',
        '索尼',
        'ソニー株式会社',
    ];

    protected static $brand_product_set = [];

    protected static $vote_set = [];

    public static function setUpBeforeClass()
    {
        $pack = service('product')->createProduct(
            'Pomodoro Time 2',
            'Focus Timer & Goal Tracker for work and study based on Pomodoro Technique',
            'http://www.pomodoro.me/'
        );
        $eid = $pack->getItem('eid');

        self::$product_eid = $eid;
    }

    public static function tearDownAfterClass()
    {
        service('product')->deleteProductByEid(self::$product_eid);

        foreach (self::$brand_titles as $title) {
            service('brand')->deleteBrand(['title' => $title]);
        }
    }

    public function testSaveBrandProductError()
    {
        $brand_service = service('brand');

        $data = [];
        $pack = $brand_service->saveBrandProduct($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-positive', $pack->getError('product_eid'));

        $data = ['product_eid' => self::$product_eid];
        $pack = $brand_service->saveBrandProduct($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('title'));
    }

    public function testSaveBrandProduct()
    {
        $brand_service = service('brand');
        $user_service = service('user');
        $max = count(self::$brand_titles) - 1;

        foreach (config()->get('tester.available')->all() as $tester) {
            $user_service->switchUserByEmail($tester['email']);
            $title = self::$brand_titles[rand(0, $max)];
            $data = [
                'product_eid' => self::$product_eid,
                'title' => $title
            ];
            $pack = $brand_service->saveBrandProduct($data);
            $this->assertTrue($pack->isOk());
            if ($pack->isOk()) {
                $this->localSaveBrandProduct($user_service->getCurrentUid(), $data);
                $this->localVoteBrandProduct($user_service->getCurrentUid(), $pack->getItem('brand_product_id'));
            }
            if (!$pack->isOk()) {
                var_dump($pack);
            }

            $pack = $brand_service->saveBrandProduct($data);
            $this->assertFalse($pack->isOk());
            $this->assertEquals('duplicated', $pack->getError('vote'));
        }
    }

    public function testVoteBrandProduct()
    {
        $product = service('product')->getEntityByEid(self::$product_eid);
        $brand_service = service('brand');
        $user_service = service('user');

        $testers = array_values(config()->get('tester.available')->all());
        $max = count($testers) - 1;

        $brand_product_set = $product->getBrandProductSet();
        $brand_product_set->setCountPerPage(0);

        //$latest_brand_product = null;

        foreach ($brand_product_set->getItems() as $brand_product) {
            $tester = $testers[rand(0, $max)];
            $user_service->switchUserByEmail($tester['email']);
            $current_uid = $user_service->getCurrentUid();

            if (!$brand_product->hasVoted()) {
                $pack = $brand_service->voteBrandProduct($brand_product->id);
                $this->assertTrue($pack->isOk());
                if ($pack->isOk()) {
                    $this->localVoteBrandProduct($current_uid, $brand_product->id);
                }
            }

            $pack = $brand_service->voteBrandProduct($brand_product->id);
            $this->assertFalse($pack->isOk());
            $this->assertEquals('duplicated', $pack->getError('vote'));

            $tester = $testers[rand(0, $max)];
            $user_service->switchUserByEmail($tester['email']);
            $current_uid = $user_service->getCurrentUid();

            if ($brand_product->hasVoted()) {
                $pack = $brand_service->unvoteBrandProduct($brand_product->id);
                $this->assertTrue($pack->isOk());
                if ($pack->isOk()) {
                    $this->localUnvoteBrandProduct($current_uid, $brand_product->id);
                }
            }

            $pack = $brand_service->unvoteBrandProduct($brand_product->id);
            $this->assertFalse($pack->isOk());
            $this->assertEquals('not-voted', $pack->getError('vote'));
        }
    }

    public function testGetBrandProductSet()
    {
        $product = service('product')->getEntityByEid(self::$product_eid);
        $brand_service = service('brand');
        $brand_product_set = $product->getBrandProductSet();
        $brand_product_set->setCountPerPage(0);

        foreach ($brand_product_set->getItems() as $brand_product) {
            $this->assertEquals(
                count(self::$vote_set[$brand_product->id]),
                $brand_product->countVote()
            );
            $this->assertArrayHasKey($brand_product->getBrandTitle(), self::$brand_product_set[$brand_product->product_eid]);
        }
    }

    protected function localSaveBrandProduct($uid, $data = [])
    {
        $product_eid = prop($data, 'product_eid', 0);
        $title = prop($data, 'title', '');
        self::$brand_product_set[$product_eid][$title][$uid] = 1;
    }

    protected function localVoteBrandProduct($uid, $brand_product_id)
    {
        self::$vote_set[$brand_product_id][$uid] = 1;
    }

    protected function localUnvoteBrandProduct($uid, $brand_product_id)
    {
        if ($this->localHasVoted($uid, $brand_product_id)) {
            unset(self::$vote_set[$brand_product_id][$uid]);
        }
    }

    protected function localHasVoted($uid, $brand_product_id)
    {
        if (isset(self::$vote_set[$brand_product_id])
            && isset(self::$vote_set[$brand_product_id][$uid])
            && (1 === self::$vote_set[$brand_product_id][$uid])
        ) {
            return true;
        }

        return false;
    }
}
