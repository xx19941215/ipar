<?php

namespace Admin\Ui;

class ProductPurchaseController extends AdminControllerBase
{
    public function purchase()
    {
        $product = $this->getProductFromParam();
        $eid = $this->getParam('eid');
        $product_purchase_set = $this->service('product_purchase')->search(['eid' => $eid]);

        return $this->page('product/purchase', [
            'product' => $product,
            'product_purchase_set' => $product_purchase_set
        ]);
    }

    public function add()
    {
        $product = $this->getProductFromParam();

        return $this->page('product/add-purchase', [
            'product' => $product,
            'product_purchase' => arr2dto([], 'product_purchase')
        ]);

    }

    public function addPost()
    {
        $data = $this->getRequestData();
        $pack = $this->service('product_purchase')->create($data);
        $product = $this->getProductFromParam();

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-product-purchase', ['eid' => $product->eid]);
        }

        return $this->page('product/add-purchase', [
            'product' => $product,
            'product_purchase' => arr2dto($data, 'product_purchase'),
            'errors' => $pack->getErrors()
        ]);
    }

    public function edit()
    {
        $product = $this->getProductFromParam();
        $product_purchase = $this->getProductPurchaseParam();

        return $this->page('product/edit-purchase', [
            'product' => $product,
            'product_purchase' => $product_purchase
        ]);
    }


    public function editPost()
    {
        $product = $this->getProductFromParam();
        $data = $this->getRequestData();
        $pack = $this->service('product_purchase')->update(['id' => $data['id']], $data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-product-purchase', ['eid' => $product->eid]);
        }

        return $this->page('product/edit-purchase', [
            'product' => $product,
            'product_purchase' => arr2dto($data, 'product_purchase'),
            'errors' => $pack->getErrors()
        ]);
    }

    public function delete()
    {
        $product = $this->getProductFromParam();
        $product_purchase = $this->getProductPurchaseParam();

        return $this->page('product/delete-purchase', [
            'product' => $product,
            'product_purchase' => $product_purchase
        ]);
    }

    public function deletePost()
    {
        $product = $this->getProductFromParam();
        $product_purchase = $this->getProductPurchaseParam();
        $pid = $this->request->request->get('id');
        $pack = $this->service('product_purchase')->delete(['id' => $pid]);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-product-purchase', ['eid' => $product->eid]);
        }

        return $this->page('product/delete-purchase', [
            'product' => $product,
            'product_purchase' => $product_purchase,
            'errors' => $pack->getErrors()
        ]);
    }

    // protected

    protected function getProductFromParam()
    {
        $eid = $this->getParam('eid');

        return $this->service('product')->getProductByEid($eid);
    }

    protected function getRequestData()
    {
        $post = $this->request->request;

        return [
            'id' => (int)$post->get('id'),
            'eid' => $post->get('eid'),
            'purchase_url' => $post->get('purchase_url'),
            'website' => $post->get('website'),
            'currency' => $post->get('currency'),
            'price' => $post->get('price'),
            'commission' => $post->get('commission'),
            'started' => $post->get('started'),
            'expired' => $post->get('expired')
        ];
    }

    protected function getProductPurchaseParam()
    {
        $pid = $this->getParam('pid');
        return $this->service('product_purchase')->findOne(['id' => $pid]);
    }
}