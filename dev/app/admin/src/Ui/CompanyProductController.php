<?php

namespace Admin\Ui;

class CompanyProductController extends AdminControllerBase
{
    protected $company_product_service;

    public function list()
    {
        $page = $this->request->query->get('page');
        $company = $this->getCompanyFromParam();
        $search = $this->request->query->get('query');
        $product_set = $this->service('company_product')->searchExistProductSet(['search' => $search, 'gid' => $company->gid]);
        $product_set->setCurrentPage($page);

        return $this->page('company_product/show', [
            'company' => $company,
            'select' => 'product',
            'product_set' => $product_set,
        ]);
    }

    public function link()
    {
        $page = $this->request->query->get('page');
        $company = $this->getCompanyFromParam();
        $search = $this->request->query->get('query');

        if (isset($_COOKIE['query'])) {
            $search = $_COOKIE['query'];
        }

        if ($this->request->query->get('query')) {
            $search = $this->request->query->get('query');
        }

        setcookie("query", "$search", time() + 3600);
        $exist_product_set = $this->service('company_product')->searchExistProductSet(['search' => $search, 'gid' => $company->gid]);
        $exist_product_set->setCountPerPage(0);
        $select_product_set = $this->service('company_product')->selectProductSet(['search' => $search, 'exclude_gid' => $company->gid]);
        $select_product_set->setCurrentPage($page);

        return $this->page('company_product/link', [
            'company' => $company,
            'select' => 'product',
            'select_product_set' => $select_product_set,
            'exist_product_set' => $exist_product_set
        ]);
    }

    public function addPost()
    {
        $company = $this->getCompanyFromParam();
        $data = $this->getRequestFromParam();
        $product_set = $this->service('company_product')->selectProductSet($company->gid);
        $pack_product = $this->service('product')->createProduct($data['title'], $data['content'], $data['url']);

        if (!$pack_product->isOk()) {
            return $this->page('company_product/link', [
                'company' => $company,
                'select' => 'product',
                'product_set' => $product_set,
                'errors' => $pack_product->getErrors()
            ]);
        }

        $data['eid'] = $pack_product->getItem('eid');
        $pack = $this->service('company_product')->save($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_product-link', ['gid' => $company->gid]);
        }

        return $this->page('company_product/link', [
            'company' => $company,
            'select' => 'product',
            'product_set' => $product_set,
            'errors' => $pack->getErrors()
        ]);

    }

    public function linkPost()
    {
        $company = $this->getCompanyFromParam();
        $data = $this->getRequestFromParam();
        $pack = $this->service('company_product')->save($data);


        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_product-link', ['gid' => $company->gid]);
        }

        $product_set = $this->service('company_product')->selectProductSet($company->gid);
        return $this->page('company_product/link', [
            'company' => $company,
            'select' => 'product',
            'product_set' => $product_set,
            'errors' => $pack->getErrors()
        ]);
    }

    public function unlink()
    {
        $gid = $this->getParam('gid');
        $eid = $this->getParam('eid');
        $company_product = $this->service('company_product')->findOne(['gid' => $gid, 'eid' => $eid]);
        $product = $this->service('entity')->getEntityByEid($eid);

        return $this->page('company_product/unlink', [
            'product' => $product,
            'company_product' => $company_product
        ]);
    }

    public function unlinkPost()
    {
        $data = $this->getRequestFromParam();
        $pack = $this->service('company_product')->delete(['id' => $data['id']]);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_product-list', ['gid' => $data['gid']]);
        }

        return $this->page('company_product/unlink', [
            'select' => 'product',
            'error' => $pack->getErrors()
        ]);
    }

    public function getRequestFromParam()
    {
        $post = $this->request->request;

        return [
            'id' => (int)$post->get('id'),
            'gid' => (int)$post->get('gid'),
            'eid' => (int)$post->get('eid'),
            'title' => $post->get('title'),
            'url' => $post->get('url'),
            'content' => $post->get('content'),
        ];
    }

    public function getCompanyFromParam()
    {
        $gid = $this->getParam('gid');
        $company = $this->service('company')->findOne(['gid' => $gid]);

        return $company;
    }

}