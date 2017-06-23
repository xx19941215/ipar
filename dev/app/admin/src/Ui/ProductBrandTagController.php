<?php
namespace Admin\Ui;

class ProductBrandTagController extends AdminControllerBase
{
    public function show()
    {
        $product = $this->getProductFromParam();
        $brand_tag = $this->service('brand_tag_product')->productGetBrandTag(['eid' => $product->eid]);

        return $this->page('product_brand_tag/show', [
            'product' => $product,
            'brand_tag' => $brand_tag
        ]);
    }

    protected function getProductFromParam()
    {
        $eid = $this->getParam('eid');
        return $this->service('product')->getProductByEid($eid);
    }

}