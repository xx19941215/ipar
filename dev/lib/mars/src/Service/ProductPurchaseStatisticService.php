<?php
namespace Mars\Service;

class ProductPurchaseStatisticService extends \Gap\Service\ServiceBase
{
    protected $product_purchase_statistic_repo;

    public function bootstrap()
    {
        $this->product_purchase_statistic_repo = gap_repo_manager()->make('product_purchase_statistic');
    }

    public function create($data)
    {
        return $this->product_purchase_statistic_repo->create($data);
    }
}