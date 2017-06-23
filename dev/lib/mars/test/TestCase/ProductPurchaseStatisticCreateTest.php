<?php
namespace Mars\Test\TestCase;

class ProductPurchaseStatisticCreateTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/product-purchase-statistic-create-init.xml');
    }

    public function testCreate()
    {
        $product_purchase_statistic = gap_repo_manager()->make('product_purchase_statistic');
        $data = [
            'product_purchase_id' => 1,
            'uid' => '',
            'client_ip' => '127.0.0.1',
            'referer' => 'http://www.ideapar.do/en-us/product/56f89a6756674',
            'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.92 Safari/537.36'
        ];
        $pack = $product_purchase_statistic->create($data);
        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'product_purchase_statistic',
            'SELECT id,product_purchase_id,uid,client_ip,referer,user_agent FROM `product_purchase_statistic`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/product-purchase-statistic-create-expected.xml')
            ->getTable('product_purchase_statistic');
        $this->assertTablesEqual($expected_table, $query_table);
    }
}