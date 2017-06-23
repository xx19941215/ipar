<?php
namespace Admin\Ui;

class CompanyBrandTagController extends AdminControllerBase
{
    public function list()
    {
        $company = $this->getCompanyFromParam();
        $brand_tag_set = $this->service('company_brand_tag')->getCompanyBrandTagSet(['gid' => $company->gid]);

        return $this->page('company_brand_tag/index', [
            'company' => $company,
            'brand_tag_set' => $brand_tag_set
        ]);
    }

    public function save()
    {
        $company = $this->getCompanyFromParam();
        return $this->page('company_brand_tag/save', [
            'company' => $company,
        ]);
    }

    public function savePost()
    {
        $data = $this->getRequestData();
        $pack = $this->service('company_brand_tag')->save($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_brand_tag-index', ['gid' => $data['gid']]);
        }

        $company = $this->getCompanyFromParam();

        return $this->page('company_brand_tag/save', [
            'company' => $company,
            'errors' => $pack->getErrors()
        ]);
    }

    public function unlink()
    {
        $company = $this->getCompanyFromParam();
        $tag_id = $this->getParam('tag_id');
        $company_brand_tag = $this->service('company_brand_tag')->findOne(['gid' => $company->gid, 'tag_id' => $tag_id]);

        if (!$company_brand_tag) {
            die('error-request');
        }

        $brand_tag = $this->service('brand_tag')->findOne(['tag_id' => $tag_id]);

        return $this->page('company_brand_tag/unlink', [
            'company' => $company,
            'company_brand_tag' => $company_brand_tag,
            'brand_tag' => $brand_tag
        ]);
    }

    public function unlinkPost()
    {
        $data = $this->getRequestData();
        $pack = $this->service('company_brand_tag')->unlink($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_brand_tag-index', ['gid' => $data['gid']]);
        }

        $company = $this->getCompanyFromParam();
        $company_brand_tag = $this->service('company_brand_tag')->findOne(['gid' => $data['gid'], 'tag_id' => $data['tag_id']]);
        $brand_tag = $this->service('brand_tag')->findOne(['tag_id' => $data['tag_id']]);

        return $this->page('company_brand_tag/unlink', [
            'company' => $company,
            'company_brand_tag' => $company_brand_tag,
            'brand_tag' => $brand_tag,
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

    public function getRequestData()
    {
        $post = $this->request->request;
        return [
            'id' => (int)$post->get('id'),
            'gid' => (int)$post->get('gid'),
            'uid' => (int)$post->get('uid'),
            'tag_id' => (int)$post->get('tag_id'),
            'title' => $post->get('title'),
            'content' => $post->get('content')
        ];
    }
}