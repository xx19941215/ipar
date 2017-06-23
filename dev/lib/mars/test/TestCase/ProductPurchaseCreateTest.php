<?php
namespace Mars\Test\TestCase;

class ProductPurchaseCreateTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/product-purchase-create-init.xml');
    }

    public function testCreate()
    {

        $product_purchase = gap_repo_manager()->make('product_purchase');
        $data = [
            'eid' => 1,
            'purchase_url' => 'http://item.jd.com/2182165.html',
            'currency' => 1,
            'price' => 100,
            'commission' => 10,
            'started' => '2016-09-01 01:00:00',
            'expired' => '2016-09-11 01:00:00'
        ];
        $pack = $product_purchase->create($data);
        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'product_purchase',
            'SELECT id,eid,purchase_url,website,currency,price,commission,started,expired FROM `product_purchase`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/product-purchase-create-expected.xml')
            ->getTable('product_purchase');
        $this->assertTablesEqual($expected_table, $query_table);
    }

    public function testErrorPurchaseUrl()
    {
        $product_purchase = gap_repo_manager()->make('product_purchase');
        $data = [
            'eid' => 1,
            'purchase_url' => 'http:item.jd.com/2182165.html',
            'currency' => 1,
            'price' => 100,
            'commission' => 10,
            'started' => '2016-09-01 01:00:00',
            'expired' => '2016-09-11 01:00:00'
        ];
        $pack = $product_purchase->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('purchase_url'));
    }


    public function testErrorDate()
    {
        $product_purchase = gap_repo_manager()->make('product_purchase');
        $data = [
            'eid' => 1,
            'purchase_url' => 'http://item.jd.com/2182165.html',
            'currency' => 1,
            'price' => 100,
            'commission' => 10,
            'started' => '2016-09-21 01:00:00',
            'expired' => '2016-09-11 01:00:00'
        ];
        $pack = $product_purchase->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('expired cannot be early than started', $pack->getError('date'));
    }

    public function testErrorPrice(){
        $product_purchase = gap_repo_manager()->make('product_purchase');
        $data = [
            'eid' => 1,
            'purchase_url' => 'http://item.jd.com/2182165.html',
            'currency' => 1,
            'price' => 'ffff',
            'commission' => 10,
            'started' => '2016-09-01 01:00:00',
            'expired' => '2016-09-11 01:00:00'
        ];
        $pack = $product_purchase->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('is not a number', $pack->getError('price'));
    }

    public function testErrorCommission(){
        $product_purchase = gap_repo_manager()->make('product_purchase');
        $data = [
            'eid' => 1,
            'purchase_url' => 'http://item.jd.com/2182165.html',
            'currency' => 1,
            'price' => 10,
            'commission' =>'ddd',
            'started' => '2016-09-01 01:00:00',
            'expired' => '2016-09-11 01:00:00'
        ];
        $pack = $product_purchase->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('is not a number', $pack->getError('commission'));
    }
}
