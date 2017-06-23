<?php
namespace Admin\Ui;

class BrandTagProductController extends AdminControllerBase
{
    public function list()
    {
        $company = $this->getCompanyFromParam();
        $brand_tag = $this->getBrandTagFromParam();
        $tag = $this->service('tag')->findOne($brand_tag->tag_id);
        $product_set = $this->service('brand_tag_product')->getBrandTagProductSet([
            'brand_tag_id' => $brand_tag->id
        ]);
        $product_set->setCurrentPage($this->request->query->get('page'));

        return $this->page('company_brand_tag_product/index', [
            'company' => $company,
            'brand_tag' => $brand_tag,
            'tag' => $tag,
            'product_set' => $product_set
        ]);
    }

    public function link()
    {
        $company = $this->getCompanyFromParam();
        $brand_tag = $this->getBrandTagFromParam();
        $tag = $this->service('tag')->findOne($brand_tag->tag_id);
        $search = $this->request->query->get('query');
        $link_product_set = $this->service('brand_tag_product')->selectLinkProductSet([
            'search' => $search,
            'exclude_tag_id' => $brand_tag->id
        ]);
        $link_product_set->setCurrentPage($this->request->query->get('page'));
        $exist_product_set = $this->service('brand_tag_product')->getBrandTagProductSet([
            'brand_tag_id' => $brand_tag->id
        ]);
        $exist_product_set->setCountPerPage(0);

        return $this->page('company_brand_tag_product/link', [
            'company' => $company,
            'brand_tag' => $brand_tag,
            'tag' => $tag,
            'link_product_set' => $link_product_set,
            'exist_product_set' => $exist_product_set
        ]);
    }

    public function linkPost()
    {
        $company = $this->getCompanyFromParam();
        $data = $this->getRequestFromParam();
        $pack = $this->service('brand_tag_product')->link($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_brand_tag_product-link', [
                'gid' => $company->gid,
                'tag_id' => $data['tag_id']
            ]);
        }

        $brand_tag = $this->getBrandTagFromParam();
        $link_product_set = $this->service('brand_tag_product')->selectLinkProductSet([
            'exclude_tag_id' => $brand_tag->id
        ]);
        $exist_product_set = $this->service('brand_tag_product')->getBrandTagProductSet([
            'brand_tag_id' => $brand_tag->id
        ]);
        $exist_product_set->setCountPerPage(0);

        return $this->page('company_brand_tag_product/link', [
            'company' => $company,
            'errors' => $pack->getErrors(),
            'link_product_set' => $link_product_set,
            'exist_product_set' => $exist_product_set
        ]);
    }

    public function savePost()
    {
        $company = $this->getCompanyFromParam();
        $data = $this->getRequestFromParam();
        $pack_product = $this->service('product')->createProduct($data['title'], $data['content'], $data['url']);

        $brand_tag = $this->getBrandTagFromParam();
        $link_product_set = $this->service('brand_tag_product')->selectLinkProductSet([
            'exclude_tag_id' => $brand_tag->id
        ]);
        $exist_product_set = $this->service('brand_tag_product')->getBrandTagProductSet([
            'brand_tag_id' => $brand_tag->id
        ]);
        $exist_product_set->setCountPerPage(0);

        if (!$pack_product->isOk()) {
            return $this->page('company_brand_tag_product/link', [
                'company' => $company,
                'errors' => $pack_product->getErrors(),
                'link_product_set' => $link_product_set,
                'exist_product_set' => $exist_product_set
            ]);
        }

        $data['eid'] = $pack_product->getItem('eid');
        $pack = $this->service('brand_tag_product')->link($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_brand_tag_product-link', [
                'gid' => $company->gid,
                'tag_id' => $data['tag_id']
            ]);
        }

        return $this->page('company_brand_tag_product/link', [
            'company' => $company,
            'errors' => $pack->getErrors(),
            'link_product_set' => $link_product_set,
            'exist_product_set' => $exist_product_set
        ]);
    }

    public function unlink()
    {
        $company = $this->getCompanyFromParam();
        $brand_tag = $this->getBrandTagFromParam();
        $tag = $this->service('tag')->findOne($brand_tag->tag_id);
        $eid = $this->getParam('eid');
        $brand_tag_product = $this->service('brand_tag_product')->findOne([
            'brand_tag_id' => $brand_tag->id,
            'eid' => $eid
        ]);
        $product = $this->service('entity')->getEntityByEid($eid);

        return $this->page('company_brand_tag_product/unlink', [
            'company' => $company,
            'product' => $product,
            'brand_tag' => $brand_tag,
            'tag' => $tag,
            'brand_tag_product' => $brand_tag_product
        ]);
    }

    public function unlinkPost()
    {
        $data = $this->getRequestFromParam();
        $pack = $this->service('brand_tag_product')->unlink(['id' => $data['id']]);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_brand_tag_product-index', [
                'gid' => $data['gid'],
                'tag_id' => $data['tag_id']
            ]);
        }

        $company = $this->getCompanyFromParam();
        $brand_tag = $this->getBrandTagFromParam();
        $tag = $this->service('tag')->findOne($brand_tag->tag_id);
        $eid = $this->getParam('eid');
        $brand_tag_product = $this->service('brand_tag_product')->findOne([
            'brand_tag_id' => $brand_tag->id,
            'eid' => $eid
        ]);
        $product = $this->service('entity')->getEntityByEid($eid);

        return $this->page('company_brand_tag_product/unlink', [
            'company' => $company,
            'product' => $product,
            'brand_tag' => $brand_tag,
            'tag' => $tag,
            'brand_tag_product' => $brand_tag_product,
            'errors' => $pack->getErrors()
        ]);
    }

    public function getCompanyFromParam()
    {
        $gid = $this->getParam('gid');
        $company = $this->service('company')->findOne(['gid' => $gid]);

        if (!$company) {
            die('error-request');
        }

        return $company;
    }

    public function getBrandTagFromParam()
    {
        $tag_id = $this->getParam('tag_id');
        $gid = $this->getParam('gid');
        $brand_tag = $this->service('company_brand_tag')->findOne([
            'tag_id' => $tag_id,
            'gid' => $gid
        ]);

        if (!$brand_tag) {
            die('error-request');
        }

        return $brand_tag;
    }

    public function getRequestFromParam()
    {
        $post = $this->request->request;

        return [
            'id' => (int)$post->get('id'),
            'gid' => (int)$post->get('gid'),
            'tag_id' => (int)$post->get('tag_id'),
            'brand_tag_id' => (int)$post->get('brand_tag_id'),
            'eid' => (int)$post->get('eid'),
            'title' => $post->get('title'),
            'url' => $post->get('url'),
            'content' => $post->get('content'),
        ];
    }
}