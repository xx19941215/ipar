<?php

namespace Admin\Ui;

class TagProductController extends AdminControllerBase
{

    public function addTagMultiple()
    {
        $url = $_SERVER['HTTP_REFERER'];
        $product = $this->getProductFromParam();
        return $this->page('product/add-tag-multiple', ['product' => $product, 'url' => $url]);
    }

    public function addTagMultiplePost()
    {
        $product = $this->getProductFromParam();
        $data['uid'] = current_uid();
        $data['eid'] = $product->eid;
        $data['entity_type_id'] = $product->type_id;
        $url = $this->request->request->get('url');
        $data['titles'] = $this->request->request->get('multiple_title');
        $pack = $this->service('entity_tag')->saveTagMultiple($data);
        if (!$pack->isOk()) {
            return $this->page('product/add-tag-multiple', [
                'product' => $product,
                'tag' => arr2dto($data, 'tag'),
                'errors' => $pack->getErrors()
            ]);
        }

        return $this->gotoTargetUrl($url);
    }

    public function showProduct()
    {
        $page = $this->request->query->get('page');
        $tag_id = $this->getParam('tag_id');
        $tag = $this->service('tag')->findOne($tag_id);
        $entity_type_id = 3;
        $query = ['tag_id'=>$tag_id,'entity_type_id'=>$entity_type_id];

        $entity_set = $this->service('tag_entity')->search($query)
            ->setCurrentPage($page);
        return $this->page('tag/product', ['tag' => $tag, 'entity_set' => $entity_set]);
    }

    protected function getProductFromParam()
    {
        $eid = $this->getParam('eid');

        return $this->service('product')->getProductByEid($eid);
    }

}