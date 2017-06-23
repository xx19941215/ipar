<?php
namespace Mars\Service;

class ProductPurchaseService extends \Gap\Service\ServiceBase
{
    protected $product_purchase_repo;

    public function bootstrap()
    {
        $this->product_purchase_repo = gap_repo_manager()->make('product_purchase');
    }

    public function create($data)
    {
        return $this->product_purchase_repo->create($data);
    }

    public function update($query, $data)
    {
        return $this->product_purchase_repo->update($query, $data);
    }

    public function findOne($query = [], $fields = [])
    {
        return $this->product_purchase_repo->findOne($query, $fields);
    }

    public function delete($query = [])
    {
        return $this->product_purchase_repo->delete($query);
    }

    public function search($query = [])
    {
        return $this->product_purchase_repo->search($query);
    }
}