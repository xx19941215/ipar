<?php
namespace Admin\Ui;

class CompanyIndustryTagController extends AdminControllerBase
{
    public function list()
    {
        $company = $this->getCompanyFromParam();
        $search = $this->request->query->get('query');
        $industry_tag_set = $this->service('company_industry_tag')->searchCompanyIndustryTagSet(['search' => $search, 'gid' => $company->gid]);
        $industry_tag_set->setCurrentPage($this->request->query->get('page'));

        return $this->page('company_industry_tag/index', [
            'company' => $company,
            'industry_tag_set' => $industry_tag_set
        ]);
    }

    public function link()
    {
        $company = $this->getCompanyFromParam();
        $search = $this->request->query->get('query');
        $exist_industry_tag_set = $this->service('company_industry_tag')->searchCompanyIndustryTagSet(['search' => $search, 'gid' => $company->gid]);
        $exist_industry_tag_set->setCountPerPage(0);
        $select_industry_tag_set = $this->service('company_industry_tag')->selectCompanyIndustryTagSet(['search' => $search, 'exclude_gid' => $company->gid]);
        $select_industry_tag_set->setCurrentPage($this->request->query->get('page'));
        
        return $this->page('company_industry_tag/link', [
            'company' => $company,
            'select_industry_tag_set' => $select_industry_tag_set,
            'exist_industry_tag_set' => $exist_industry_tag_set
        ]);
    }

    public function linkPost()
    {
        $data = $this->getRequestData();
        $pack = $this->service('company_industry_tag')->link($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_industry_tag-link', ['gid' => $data['gid']]);
        }


        $company = $this->getCompanyFromParam();
        $industry_tag_set = $this->service('company_industry_tag')->selectCompanyIndustryTagSet(['gid' => $company->gid]);

        return $this->page('company_industry_tag/link', [
            'company' => $company,
            'industry_tag_set' => $industry_tag_set,
            'errors' => $pack->getErrors()
        ]);
    }

    public function unlink()
    {
        $company = $this->getCompanyFromParam();
        $tag_id = $this->getParam('tag_id');
        $company_industry_tag = $this->service('company_industry_tag')->findOne(['gid' => $company->gid, 'tag_id' => $tag_id]);
        $industry_tag = $this->service('industry_tag')->findOne(['tag_id' => $tag_id]);

        return $this->page('company_industry_tag/unlink', [
            'company' => $company,
            'industry_tag' => $industry_tag,
            'company_industry_tag' => $company_industry_tag
        ]);
    }

    public function unlinkPost()
    {
        $data = $this->getRequestData();
        $pack = $this->service('company_industry_tag')->unlink($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_industry_tag', ['gid' => $data['gid']]);
        }

        $company = $this->getCompanyFromParam();
        $company_industry_tag = $this->service('company_industry_tag')->findOne(['gid' => $data['gid'], 'tag_id' => $data['tag_id']]);
        $industry_tag = $this->service('industry_tag')->findOne(['tag_id' => $data['tag_id']]);

        return $this->page('company_industry_tag/unlink', [
            'company' => $company,
            'industry_tag' => $industry_tag,
            'company_industry_tag' => $company_industry_tag
        ]);

    }

    public function getCompanyFromParam()
    {
        $gid = $this->getParam('gid');
        $company = $this->service('company')->findOne(['gid' => $gid]);

        return $company;
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
}