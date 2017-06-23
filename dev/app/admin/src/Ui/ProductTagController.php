<?php

namespace Admin\Ui;

class ProductTagController extends AdminControllerBase
{
    protected $entity_tag_service;

    public function bootstrap()
    {
        $this->entity_tag_service = gap_service_manager()->make('entity_tag');
    }


    public function search()
    {
        $product = $this->getProductFromParam();
        $query = $this->request->query->get('query', '');
        $data['eid'] = $product->eid;
        $data['entity_type_id'] = $product->type_id;
        $data['query'] = $query;
        $tag_set = $this->entity_tag_service->search($data);
        return $this->page('product/tag', [
            'product' => $product,
            'tag_set' => $tag_set,
        ]);
    }

    public function addTag()
    {
        $product = $this->getProductFromParam();
        return $this->page('product/add-tag', ['product' => $product]);
    }

    public function addTagPost()
    {
        $product = $this->getProductFromParam();
        $data['tag_title'] = $this->request->request->get('title');
        $data['tag_content'] = $this->request->request->get('content');
        $data['uid'] = current_uid();
        $data['eid'] = $product->eid;
        $data['entity_type_id'] = $product->type_id;
        $pack = $this->entity_tag_service->save($data);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-product_tag-search', ['eid' => $product->eid]);
        }
        $data['errors'] = $pack->getErrors();
        return $this->page('product/add-tag', [
            'product' => $product,
            'tag' => arr2dto($data, 'tag'),
            'errors' => $pack->getErrors()
        ]);
    }

    public function unlink()
    {
        $tag_id = $this->getParam('tag_id');
        $product = $this->getProductFromParam();
        $tag = gap_service_manager()->make('tag')->findOne($tag_id);
        return $this->page('product/unlink', ['product' => $product, 'tag' => $tag]);
    }

    public function unlinkPost()
    {
        $eid = $this->request->request->get('eid');
        $tag_id = $this->request->request->get('tag_id');
        $pack = $this->entity_tag_service->delete(['eid' => $eid, 'tag_id' => $tag_id]);
        if (!$pack->isOk()) {
            return $this->page('product/unlink', ['errors' => $pack->getErrors()]);
        }
        return $this->gotoRoute('admin-ui-rqt_tag-search', ['eid' => $eid]);

    }


    protected function getProductFromParam()
    {
        $eid = $this->getParam('eid');
        return $this->service('product')->getProductByEid($eid);
    }
}