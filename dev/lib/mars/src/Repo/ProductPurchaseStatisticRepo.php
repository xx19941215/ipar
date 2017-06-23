<?php

namespace Mars\Repo;

class ProductPurchaseStatisticRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'product_purchase_statistic';
    protected $dto = 'product_purchase_statistic';
    protected $fields = [
        'product_purchase_id' => 'int',
        'uid' => 'int',
        'client_ip' => 'str',
        'referer' => 'str',
        'user_agent' => 'str'
    ];

    protected function validate($data)
    {

    }
}