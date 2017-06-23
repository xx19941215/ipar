<?php

namespace Admin\Ui;

class ProductController extends AdminControllerBase
{
    public function index()
    {
        $query = $this->request->query->get('query');
        $product_set = $this->service('product')->schProductSet(['query' => $query, 'status' => null, 'sort' => 1]);
        $product_set->setCurrentPage((int)$this->request->get('page'));

        return $this->page('product/index', [
            'product_set' => $product_set,
            'query' => $query
        ]);
    }

    public function search()
    {
        return $this->page('product/search');
    }

    public function add()
    {
        $this->prepareTargetUrl();
        return $this->page('product/add');
    }

    public function addPost()
    {
        $title = $this->request->request->get('title');
        $content = $this->request->request->get('content');
        $url = $this->request->get('url');

        $pack = $this->service('product')->createProduct($title, $content, $url);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-product-show', [
                'eid' => $pack->getItem('eid')
            ]);
        } else {
            return $this->page('product/add', [
                'title' => $title,
                'content' => $content,
                'url' => $url,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function show()
    {
        $product = $this->getProductFromParam();
        $story_set = gap_service_manager()->make('story')->search(['eid' => $product->eid]);
        $tag_set = $this->getTagSet();
        return $this->page('product/show', [
            'product' => $product,
            'story_set' => $story_set,
            'tag_set' => $tag_set
        ]);
    }

    public function content()
    {
        return $this->page('product/content', [
            'product' => $this->getProductFromParam()
        ]);
    }


    public function edit()
    {
        $product = $this->getProductFromParam();
        $this->referTargetUrl();

        return $this->page('product/edit', [
            'product' => $product,
            'eid' => $product->eid,
            'title' => $product->title,
            'content' => $product->content,
            'url' => $product->url,
        ]);
    }

    public function editPost()
    {
        $eid = $this->request->request->get('eid');
        $title = $this->request->request->get('title');
        $content = $this->request->request->get('content');
        $url = $this->request->request->get('url');
        $pack = $this->service('product')->updateProduct($eid, $title, $content, $url);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-product-show', ['eid' => $eid]);
        } else {
            return $this->page('product/edit', [
                'eid' => $eid,
                'title' => $title,
                'content' => $content,
                'url' => $url,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function delete()
    {
        $this->referTargetUrl();

        return $this->page('product/delete', [
            'product' => $this->getProductFromParam(),
        ]);
    }

    public function deletePost()
    {
        $param_eid = $this->getParam('eid');
        $post_eid = $this->request->request->get('eid');
        if ($param_eid != $post_eid) {
            die('error request');
        }
        $pack = $this->service('product')->deleteProduct($post_eid);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-product');
            //return $this->gotoTargetUrl('', route_url('admin-product'));
        } else {
            return $this->page('product/delete', [
                'product' => $this->getProductFromParam(),
                'errors' => $pack->getErrors()
            ]);
        }
    }

    /*
     * property
     */

    public function property()
    {
        $product = $this->getProductFromParam();
        $query = $this->request->query->get('query', '');
        $property_set = $this->service('product')->schPropertySet($product->eid, ['query' => $query, 'status' => null]);

        return $this->page('product/property', [
            'product' => $product,
            'query' => $query,
            'property_set' => $property_set,
        ]);
    }


    // feature
    public function feature()
    {
        $product = $this->getProductFromParam();
        $query = $this->request->query->get('query', '');
        $feature_set = $this->service('product')->schPfeatureSet($product->eid, ['query' => $query, 'status' => null]);

        return $this->page('product/feature', [
            'product' => $product,
            'query' => $query,
            'feature_set' => $feature_set,
        ]);
    }

    public function addFeature()
    {
        $query = $this->request->query->get('query', '');
        $feature_set = $this->service('feature')->schFeatureSet(['query' => $query, 'status' => null]);
        $this->prepareTargetUrl();
        return $this->page('product/add-feature', [
            'feature_set' => $feature_set,
            'query' => $query,
            'product' => $this->getProductFromParam()
        ]);
    }

    public function addFeaturePost()
    {
        $product = $this->getProductFromParam();
        $dst_eid = $this->request->request->get('dst_eid');
        if ($dst_eid) {
            $pack = $this->service('product')->createProperty($product->eid, 'feature', $dst_eid);
        } else {
            $title = $this->request->request->get('title');
            $content = $this->request->request->get('content');
            $pack = $this->service('product')->createPfeature($product->eid, $title, $content);
        }

        if ($pack->isOk()) {
            //return $this->gotoTargetUrl('', route_url('admin-product-feature', ['eid' => $product->eid]));
            return $this->gotoRoute('admin-feature-show', ['eid' => $pack->getItem('feature_eid')]);
            exit();
        }

        return $this->page('product/add-feature', [
            'product' => $product,
            'title' => $title,
            'content' => $content,
            'errors' => $pack->getErrors(),
        ]);

    }

    // improving

    public function improving()
    {
        $product = $this->getProductFromParam();
        $query = $this->request->query->get('query', '');
        $improving_set = $this->service('product')->schPimprovingSet($product->eid, ['query' => $query, 'status' => null]);

        return $this->page('product/improving', [
            'product' => $product,
            'query' => $query,
            'improving_set' => $improving_set
        ]);
    }

    public function addImproving()
    {
        $rqt_set = $this->service('rqt')->schRqtSet([
            'query' => $this->request->query->get('query'),
            'status' => null
        ]);
        $this->prepareTargetUrl();
        return $this->page('product/add-improving', [
            'rqt_set' => $rqt_set,
            'product' => $this->getProductFromParam()
        ]);
    }

    public function addImprovingPost()
    {
        $product = $this->getProductFromParam();
        $dst_eid = $this->request->request->get('dst_eid');
        if ($dst_eid) {
            $pack = $this->service('product')->createProperty($product->eid, 'improving', $dst_eid);
        } else {
            $title = $this->request->request->get('title');
            $content = $this->request->request->get('content');
            $pack = $this->service('product')->createPimproving($product->eid, $title, $content);
        }

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-rqt-show', ['eid' => $pack->getItem('rqt_eid')]);
            //return $this->gotoTargetUrl('', route_url('admin-product-improving', ['eid' => $product->eid]));
            exit();
        } else {
            return $this->page('product/add-improving', [
                'product' => $product,
                'title' => $title,
                'content' => $content,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    // solved

    public function solved()
    {
        $product = $this->getProductFromParam();
        $query = $this->request->query->get('query', '');
        $solved_set = $this->service('product')->schPsolvedSet($product->eid, ['query' => $query, 'status' => null]);

        return $this->page('product/solved', [
            'product' => $product,
            'query' => $query,
            'solved_set' => $solved_set
        ]);
    }

    public function addSolved()
    {
        $rqt_set = $this->service('rqt')->schRqtSet([
            'query' => $this->request->query->get('query'),
            'status' => null
        ]);

        $this->prepareTargetUrl();

        return $this->page('product/add-solved', [
            'rqt_set' => $rqt_set,
            'product' => $this->getProductFromParam()
        ]);
    }

    public function addSolvedPost()
    {
        $product = $this->getProductFromParam();
        $dst_eid = $this->request->request->get('dst_eid');
        if ($dst_eid) {
            $pack = $this->service('product')->createProperty($product->eid, 'solved', $dst_eid);
        } else {
            $title = $this->request->request->get('title');
            $content = $this->request->request->get('content');
            $pack = $this->service('product')->createPsolved($product->eid, $title, $content);
        }

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-rqt-show', ['eid' => $pack->getItem('rqt_eid')]);
            //return $this->gotoTargetUrl('', route_url('admin-product-solved', ['eid' => $product->eid]));
            exit();
        } else {
            return $this->page('product/add-solved', [
                'product' => $product,
                'title' => $title,
                'content' => $content,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function pbranch()
    {
        $product = $this->getProductFromParam();
        $pbranch_set = $this->service('product')->schPbranchSet($product->eid);
        return $this->page('product/pbranch', [
            'product' => $product,
            'pbranch_set' => $pbranch_set
        ]);
    }


    public function addPbranch()
    {
        $this->prepareTargetUrl();
        $product = $this->getProductFromParam();
        return $this->page('product/add-pbranch', [
            'product' => $product,
            'product_eid' => $product->eid,
        ]);
    }

    public function addPbranchPost()
    {
        $product_eid = $this->request->request->get('product_eid');
        $title = $this->request->request->get('title');

        $pack = $this->service('product')->createPbranch(
            $product_eid,
            $title
        );

        if ($pack->isOK()) {
            return $this->gotoRoute('admin-product-pbranch', ['eid' => $product_eid]);
        } else {
            return $this->page('product/add-pbranch', [
                'product' => $this->getProductFromParam(),
                'product_eid' => $product_eid,
                'title' => $title,
                'errors' => $pack->getErrors()
            ]);
        }
    }

    public function editPbranch()
    {
        $product = $this->getProductFromParam();
        $pbranch_id = $this->getParam('pbranch_id');
        $pbranch = $this->service('product')->getPbranchById($pbranch_id);

        return $this->page('product/edit-pbranch', [
            'product' => $product,
            'product_eid' => $product->eid,
            'pbranch_id' => $pbranch_id,
            'title' => $pbranch->title
        ]);
    }

    public function editPbranchPost()
    {
        $pbranch_id = $this->getParam('pbranch_id');
        $title = $this->request->request->get('title');
        $pack = $this->service('product')->updatePbranch(
            $pbranch_id,
            $title
        );

        if ($pack->isOK()) {
            return $this->gotoRoute('admin-product-pbranch', ['eid' => $this->getParam('eid')]);
        } else {
            return $this->page('product/add-pbranch', [
                'product' => $this->getProductFromParam(),
                'product_eid' => $product_eid,
                'title' => $title,
                'errors' => $pack->getErrors()
            ]);
        }
    }

    public function ptag()
    {
        $product = $this->getProductFromParam();
        $ptag_set = $this->service('product')->schPtagSet($product->eid);
        return $this->page('product/ptag', [
            'product' => $product,
            // 'query' => $query,
            'ptag_set' => $ptag_set
        ]);
    }


    public function addPtag()
    {
        $this->prepareTargetUrl();

        $product = $this->getProductFromParam();
        return $this->page('product/add-ptag', [
            'product' => $product,
            'product_eid' => $product->eid,
        ]);
    }

    public function addPtagPost()
    {
        $product_eid = $this->request->request->get('product_eid');
        $title = $this->request->request->get('title');

        $pack = $this->service('product')->createPtag(
            $product_eid,
            $title
        );

        if ($pack->isOK()) {
            return $this->gotoRoute('admin-product-ptag', ['eid' => $product_eid]);
        } else {
            return $this->page('product/add-ptag', [
                'product' => $this->getProductFromParam(),
                'product_eid' => $product_eid,
                'title' => $title,
                'errors' => $pack->getErrors()
            ]);
        }

    }

    public function editPtag()
    {
        $product = $this->getProductFromParam();
        $ptag_id = $this->getParam('ptag_id');
        $ptag = $this->service('product')->getPtagById($ptag_id);

        return $this->page('product/edit-ptag', [
            'product' => $product,
            'product_eid' => $product->eid,
            'ptag_id' => $ptag_id,
            'title' => $ptag->title
        ]);
    }

    public function editPtagPost()
    {
        $ptag_id = $this->getParam('ptag_id');
        $title = $this->request->request->get('title');
        $pack = $this->service('product')->updatePtag(
            $ptag_id,
            $title
        );

        if ($pack->isOK()) {
            return $this->gotoRoute('admin-product-ptag', ['eid' => $this->getParam('eid')]);
        } else {
            return $this->page('product/add-ptag', [
                'product' => $this->getProductFromParam(),
                'product_eid' => $product_eid,
                'title' => $title,
                'errors' => $pack->getErrors()
            ]);
        }
    }

    public function ptarget()
    {
        $product = $this->getProductFromParam();
        $ptarget_set = $this->service('product')->schPtargetSet($product->eid);
        return $this->page('product/ptarget', [
            'product' => $product,
            // 'query' => $query,
            'ptarget_set' => $ptarget_set
        ]);
    }


    public function addPtarget()
    {
        $this->prepareTargetUrl();

        $product = $this->getProductFromParam();
        return $this->page('product/add-ptarget', [
            'product' => $product,
            'product_eid' => $product->eid,
        ]);
    }

    public function addPtargetPost()
    {
        $product_eid = $this->request->request->get('product_eid');
        $title = $this->request->request->get('title');

        $pack = $this->service('product')->createPtarget(
            $product_eid,
            $title
        );

        if ($pack->isOK()) {
            return $this->gotoRoute('admin-product-ptarget', ['eid' => $product_eid]);
        } else {
            return $this->page('product/add-ptarget', [
                'product' => $this->getProductFromParam(),
                'product_eid' => $product_eid,
                'title' => $title,
                'errors' => $pack->getErrors()
            ]);
        }

    }

    public function editPtarget()
    {
        $product = $this->getProductFromParam();
        $ptarget_id = $this->getParam('ptarget_id');
        $ptarget = $this->service('product')->getPtargetById($ptarget_id);

        return $this->page('product/edit-ptarget', [
            'product' => $product,
            'product_eid' => $product->eid,
            'ptarget_id' => $ptarget_id,
            'title' => $ptarget->title
        ]);
    }

    public function editPtargetPost()
    {
        $ptarget_id = $this->getParam('ptarget_id');
        $title = $this->request->request->get('title');
        $pack = $this->service('product')->updatePtarget(
            $ptarget_id,
            $title
        );

        if ($pack->isOK()) {
            return $this->gotoRoute('admin-product-ptarget', ['eid' => $this->getParam('eid')]);
        } else {
            return $this->page('product/add-ptarget', [
                'product' => $this->getProductFromParam(),
                'product_eid' => $product_eid,
                'title' => $title,
                'errors' => $pack->getErrors()
            ]);
        }
    }


    public function getTagSet()
    {
        $product = $this->getProductFromParam();
        $data['eid'] = $product->eid;
        $data['entity_type_id'] = $product->type_id;
        $tag_set = gap_service_manager()->make('entity_tag')->search($data);
        return $tag_set;
    }


    // protected

    protected function getProductFromParam()
    {
        $eid = $this->getParam('eid');

        return $this->service('product')->getProductByEid($eid);
    }
}
