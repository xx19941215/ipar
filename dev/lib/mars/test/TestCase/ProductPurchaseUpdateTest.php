<?php
namespace Mars\Test\TestCase;

class ProductPurchaseUpdateTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/product-purchase-update-init.xml');
    }

    public function testUpdate()
    {

        $product_purchase = gap_repo_manager()->make('product_purchase');
        $data = [
            'eid' => 1,
            'purchase_url' => 'http://sale.jd.com/act/4VRdmG362EbLpIx.html',
            'currency' => 1,
            'price' => 200,
            'commission' => 20,
            'started' => '2016-09-01 01:00:00',
            'expired' => '2016-09-11 01:00:00'
        ];
        $pack = $product_purchase->update(['eid' => $data['eid']],$data);
        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'product_purchase',
            'SELECT id,eid,purchase_url,website,currency,price,commission,started,expired FROM `product_purchase`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/product-purchase-update-expected.xml')
            ->getTable('product_purchase');
        $this->assertTablesEqual($expected_table, $query_table);
    }

    public function testErrorPurchaseUrl()
    {
        $product_purchase = gap_repo_manager()->make('product_purchase');
        $data = [
            'eid' => 1,
            'purchase_url' => 'http:sale.jd.com/act/4VRdmG362EbLpIx.html',
            'currency' => 1,
            'price' => 200,
            'commission' => 20,
            'started' => '2016-09-01 01:00:00',
            'expired' => '2016-09-11 01:00:00'
        ];
        $pack = $product_purchase->update(['eid' => $data['eid']],$data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('purchase_url'));
    }

    public function testErrorDate()
    {
        $product_purchase = gap_repo_manager()->make('product_purchase');
        $data = [
            'eid' => 1,
            'purchase_url' => 'http://sale.jd.com/act/4VRdmG362EbLpIx.html',
            'currency' => 1,
            'price' => 200,
            'commission' => 20,
            'started' => '2016-09-21 01:00:00',
            'expired' => '2016-09-11 01:00:00'
        ];
        $pack = $product_purchase->update(['eid' => $data['eid']],$data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('expired cannot be early than started', $pack->getError('date'));
    }

    public function testErrorPrice()
    {
        $product_purchase = gap_repo_manager()->make('product_purchase');
        $data = [
            'eid' => 1,
            'purchase_url' => 'http://sale.jd.com/act/4VRdmG362EbLpIx.html',
            'currency' => 1,
            'price' => 'fff',
            'commission' => 20,
            'started' => '2016-09-01 01:00:00',
            'expired' => '2016-09-11 01:00:00'
        ];
        $pack = $product_purchase->update(['eid' => $data['eid']],$data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('is not a number', $pack->getError('price'));
    }

    public function testErrorCommission()
    {
        $product_purchase = gap_repo_manager()->make('product_purchase');
        $data = [
            'eid' => 1,
            'purchase_url' => 'http://sale.jd.com/act/4VRdmG362EbLpIx.html',
            'currency' => 1,
            'price' => 10,
            'commission' => '1ff',
            'started' => '2016-09-01 01:00:00',
            'expired' => '2016-09-11 01:00:00'
        ];
        $pack = $product_purchase->update(['eid' => $data['eid']],$data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('is not a number', $pack->getError('commission'));
    }
}
