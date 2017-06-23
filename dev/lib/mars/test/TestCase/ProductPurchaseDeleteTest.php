<?php
namespace Mars\Test\TestCase;

class ProductPurchaseDeleteTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/product-purchase-delete-init.xml');
    }

    public function testDelete()
    {

        $product_purchase = gap_repo_manager()->make('product_purchase');
        $pack = $product_purchase->delete(['id' => 1]);
        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'product_purchase',
            'SELECT id,eid,purchase_url,website,currency,price,commission,started,expired FROM `product_purchase`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/product-purchase-delete-expected.xml')
            ->getTable('product_purchase');
        $this->assertTablesEqual($expected_table, $query_table);
    }
}
