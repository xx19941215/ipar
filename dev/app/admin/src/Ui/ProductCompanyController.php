<?php
namespace Admin\Ui;

class ProductCompanyController extends AdminControllerBase
{
    public function list()
    {
        $page = $this->request->query->get('page');
        $product = $this->getProductFromParam();

        $search = $this->request->query->get('query');
        $company_set = $this->service('company_product')->searchExistCompanySet(['search' => $search, 'eid' => $product->eid]);
        $company_set->setCurrentPage($page);

        return $this->page('product_company/show', [
            'product' => $product,
            'company_set' => $company_set
        ]);
    }

    public function link()
    {
        $page = $this->request->query->get('page');
        $product = $this->getProductFromParam();
        $search = $this->request->query->get('query');
        $company_set = $this->service('company_product')->selectCompanySet(['search' => $search, 'eid' => $product->eid]);
        $company_set->setCurrentPage($page);

        return $this->page('product_company/link', [
            'product' => $product,
            'company_set' => $company_set
        ]);
    }

    public function linkPost()
    {
        $product = $this->getProductFromParam();
        $data = $this->getRequestFromParam();
        $pack = $this->service('company_product')->save($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-product_company-link', ['eid' => $product->eid]);
        }

        return $this->page('product_company/link', [
            'product' => $product,
            'select' => 'product',
            'errors' => $pack->getErrors()
        ]);
    }

    public function unlink()
    {
        $product = $this->getProductFromParam();
        $gid = $this->getParam('gid');
        $company_product = $this->service('company_product')->findOne(['eid' => $product->eid, 'gid' => $gid]);
        $company = $this->service('company')->findOne(['gid' => $company_product->gid]);

        return $this->page('product_company/unlink', [
            'product' => $product,
            'company' => $company,
            'company_product' => $company_product
        ]);
    }

    public function unlinkPost()
    {
        $data = $this->getRequestFromParam();
        $pack = $this->service('company_product')->delete(['id' => $data['id']]);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-product_company', ['eid' => $data['eid']]);
        }

        $product = $this->getProductFromParam();
        $company = $this->service('company')->findOne(['gid' => $data['gid']]);

        return $this->page('product_company/unlink', [
            'product' => $product,
            'company' => $company,
            'errors' => $pack->getErrors()
        ]);

    }

    protected function getProductFromParam()
    {
        $eid = $this->getParam('eid');
        return $this->service('product')->getProductByEid($eid);
    }


    public function getRequestFromParam()
    {
        $post = $this->request->request;

        return [
            'id' => (int)$post->get('id'),
            'gid' => (int)$post->get('gid'),
            'eid' => (int)$post->get('eid'),
        ];
    }
}
