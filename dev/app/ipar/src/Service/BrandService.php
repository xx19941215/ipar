<?php
namespace Ipar\Service;

class BrandService extends IparServiceBase
{
    public function schBrandProductSet($query = [])
    {
        return $this->dataSet(
            $this->repo('brand')->schBrandProductSsb($query)
        );
    }

    public function findBrandProduct($query = [])
    {
    }

    public function saveBrandProduct($data)
    {
        if (true !== ($validated = $this->validate($data))) {
            return $validated;
        }
        return $this->repo('brand')->saveBrandProduct($data);
    }

    public function voteBrandProduct($brand_product_id)
    {
        return $this->repo('brand')->voteBrandProduct($brand_product_id);
    }

    public function unvoteBrandProduct($brand_product_id)
    {
        return $this->repo('brand')->unvoteBrandProduct($brand_product_id);
    }

    public function hasVoted($brand_product_id)
    {
        return $this->repo('brand')->hasVoted($brand_product_id);
    }

    public function deleteBrandProduct($query = [])
    {
        return $this->repo('brand')->deleteBrandProduct($query);
    }

    public function deleteBrand($query)
    {
        return $this->repo('brand')->deleteBrand($query);
    }

    public function fetchBrandMain($brand_id, $locale_id = 0)
    {

        if ($cached = $this->fetchCachedBrandMain($brand_id, $locale_id)) {
            return $cached;
        }

        $brand_main = $this->repo('brand')->fetchBrandMain($brand_id, $locale_id);
        $this->setCachedBrandMain($brand_id, $locale_id, $brand_main);
        return $brand_main;
    }

    public function fetchBrandTitle($brand_id, $locale_id = 0)
    {
        $brand_main = $this->fetchBrandMain($brand_id, $locale_id);
        return $brand_main->title;
    }

    public function findBrandMain($query)
    {
        return $this->repo('brand')->findBrandMain($query);
    }

    protected function fetchCachedBrandMain($brand_id, $locale_id)
    {
        if ($str = $this->cache()->hGet("b-$brand_id", "$locale_id")) {
            $brand_main = dto_decode($str, 'brand_main');
            return $brand_main;
        }

        return null;
    }

    protected function setCachedBrandMain($brand_id, $locale_id, $brand_main)
    {
        $this->cache()->hSet("b-$brand_id", "$locale_id", json_encode($brand_main));
    }

    protected function deleteCachedBrand($brand_id)
    {
        $this->cache()->delete("b-$brand_id");
    }

    protected function validate(&$data)
    {
        if (!$product_eid = (int) prop($data, 'product_eid', 0)) {
            return $this->packError('product_eid', 'not-positive');
        }
        $data['product_eid'] = $product_eid;

        if (!$title = trim(prop($data, 'title', ''))) {
            return $this->packEmpty('title');
        }
        $data['title'] = $title;

        return true;
    }
}
