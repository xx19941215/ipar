<?php
namespace Mars\Service;

class BrandTagProductService extends \Gap\Service\ServiceBase
{

    protected $brand_tag_product_table_repo;

    public function bootstrap()
    {
        $this->brand_tag_product_table_repo = gap_repo_manager()->make('brand_tag_product');
    }

    public function getBrandTagProductSet($query)
    {
        return $this->brand_tag_product_table_repo->getBrandTagProductSet($query);
    }

    public function selectLinkProductSet($query)
    {
        return $this->brand_tag_product_table_repo->selectLinkProductSet($query);
    }

    public function link($data)
    {
        return $this->brand_tag_product_table_repo->create($data);
    }

    public function unlink($query)
    {
        return $this->brand_tag_product_table_repo->delete($query);
    }

    public function findOne($query)
    {
        return $this->brand_tag_product_table_repo->findOne($query);
    }

    public function productGetBrandTag($query)
    {
        return $this->brand_tag_product_table_repo->productGetBrandTag($query);
    }
}

