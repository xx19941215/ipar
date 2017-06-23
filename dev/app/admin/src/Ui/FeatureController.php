<?php

namespace Admin\Ui;

class FeatureController extends AdminControllerBase
{
    public function index()
    {
        $query = $this->request->query->get('query');
        $feature_set = $this->service('feature')->schFeatureSet([
            'query' => $query, 
            'status' => null
        ]);

        $feature_set->setCurrentPage((int) $this->request->get('page'));

        return $this->page('feature/index', [
            'feature_set' => $feature_set,
            'query' => $query,
        ]);
    }

    public function add()
    {
        $this->prepareTargetUrl();
        return $this->page('feature/add');
    }

    public function addPost()
    {
        $title = $this->request->request->get('title');
        $content = $this->request->request->get('content');
        $pack = $this->service('feature')->createFeature($title, $content);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-feature-show', [
                'eid' => $pack->getItem('eid')
            ]);
        } else {
            return $this->page('feature/add', [
                'title' => $title,
                'content' => $content,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function show()
    {
        $feature = $this->getFeatureFromParam();
        $story_set = gap_service_manager()->make('story')->search(['eid' => $feature->eid]);
        return $this->page('feature/show', [
            'feature' => $feature,
            'story_set' => $story_set
        ]);
    }

    public function content()
    {
        return $this->page('feature/content', [
            'feature' => $this->getFeatureFromParam()
        ]);
    }

    public function edit()
    {
        $feature = $this->getFeatureFromParam();
        $this->prepareTargetUrl();

        return $this->page('feature/edit', [
            'eid' => $feature->eid,
            'title' => $feature->title,
            'content' => $feature->content,
        ]);
    }

    public function editPost()
    {
        $eid = $this->request->request->get('eid');
        $title = $this->request->request->get('title');
        $content = $this->request->request->get('content');
        $pack = $this->service('feature')->updateFeature($eid, $title, $content);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-feature-show', ['eid' => $eid]);
        } else {
            return $this->page('feature/edit', [
                'eid' => $eid,
                'title' => $title,
                'content' => $content,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function delete()
    {
        if ($referer = $this->request->headers->get('referer')) {
            $this->setTargetUrl($referer);
        }
        return $this->page('feature/delete', [
            'feature' => $this->getFeatureFromParam(),
        ]);
    }

    public function deletePost()
    {
        $param_eid = $this->getParam('eid');
        $post_eid = $this->request->request->get('eid');
        if ($param_eid != $post_eid) {
            die('error request');
        }
        $pack = $this->service('feature')->deleteFeature($post_eid);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-feature');
            //return $this->gotoTargetUrl('', route_url('admin-feature'));
        } else {
            return $this->page('feature/delete', [
                'feature' => $this->getFeatureFromParam(),
                'errors' => $pack->getErrors()
            ]);
        }
    }

    public function product()
    {
        $feature = $this->getFeatureFromParam();
        $query = $this->request->query->get('query');
        $product_set = $this->service('feature')->schFproductSet($feature->eid, ['query' => $query, 'status' => null]);
        return $this->page('feature/product', [
            'feature' => $feature,
            'query' => $query,
            'product_set' => $product_set
        ]);
    }

    public function addProduct()
    {
    }

    public function addProductPost()
    {
    }

    protected function getFeatureFromParam()
    {
        $eid = $this->getParam('eid');
        return $this->service('feature')->getEntityByEid($eid);
    }

}
