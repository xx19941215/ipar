<?php
namespace Ipar\Service;

class ProductCounterService extends IparServiceBase
{
    public function countProduct()
    {
        $cache_key = "countProduct";
        if ($count = $this->cache()->get($cache_key)) {
            return $count;
        }
        $count = $this->repo('product_counter')->countProduct();
        $this->cache()->set($cache_key, $count, 3600);
        return $count;
    }
}
