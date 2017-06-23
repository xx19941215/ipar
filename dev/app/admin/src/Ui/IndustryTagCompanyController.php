<?php
namespace Admin\Ui;

class IndustryTagCompanyController extends AdminControllerBase
{
    public function show()
    {
        $tag = $this->getTagFromParam();
        return $this->page('industry_tag_company/show', [
            'tag' => $tag,
        ]);
    }

    public function list()
    {
        $tag = $this->getTagFromParam();
        $search = $this->request->query->get('query');
        $company_set = $this->service('company_industry_tag')->existIndustryTagCompanySet(['search' => $search, 'tag_id' => $tag->id]);
        $company_set->setCurrentPage($this->request->query->get('page'));

        return $this->page('industry_tag_company/index', [
            'tag' => $tag,
            'company_set' => $company_set
        ]);
    }

    public function unlink()
    {
        $company_industry_tag = $this->getCompanyIndustrySetFromParam();
        $tag = $this->getTagFromParam();
        $company = $this->service('company')->findOne(['gid' => $company_industry_tag->gid]);

        return $this->page('industry_tag_company/unlink', [
            'tag' => $tag,
            'company' => $company,
            'company_industry_tag' => $company_industry_tag
        ]);
    }

    public function unlinkPost()
    {
        $data = $this->getRequestData();
        $pack = $this->service('company_industry_tag')->unlink($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-industry_tag_company-list', ['gid' => $data['tag_id']]);
        }

        $company = $this->getCompanyFromParam();
        $company_industry_tag = $this->service('company_industry_tag')->findOne(['gid' => $data['gid'], 'tag_id' => $data['tag_id']]);

        return $this->page('industry_tag_company/unlink', [
            'company' => $company,
            'company_industry_tag' => $company_industry_tag,
            'errors' => $pack->getErrors()
        ]);

    }


    public function getRequestData()
    {
        $post = $this->request->request;
        return [
            'id' => (int)$post->get('id'),
            'gid' => (int)$post->get('gid'),
            'tag_id' => (int)$post->get('tag_id'),
        ];
    }

    public function getTagFromParam()
    {
        $tag_id = $this->getParam('tag_id');
        $tag = $this->service('tag')->findOne($tag_id);

        return $tag;
    }

    public function getCompanyIndustrySetFromParam()
    {
        $tag_id = $this->getParam('tag_id');
        $gid = $this->getParam('gid');
        $company_industry_tag = $this->service('company_industry_tag')->findOne(['tag_id' => $tag_id, 'gid' => $gid]);

        return $company_industry_tag;
    }

    public function getCompanyFromParam()
    {
        $gid = $this->getParam('gid');
        $company = $this->service('company')->findOne(['gid' => $gid]);

        return $company;
    }
}