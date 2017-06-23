<?php

namespace Admin\Ui;

class CompanyOfficeController extends AdminControllerBase
{

    protected $group_office_service;

    public function bootstrap()
    {
        $this->group_office_service = gap_service_manager()->make('group_office');
    }


    public function list()
    {
        $company = $this->getCompanyFromParam();
        $group_office_set = $this->group_office_service->search(['gid' => $company->gid]);

        return $this->page('company_office/show', [
            'company' => $company,
            'group_office_set' => $group_office_set,
            'select' => 'office'
        ]);
    }

    public function add()
    {
        $company = $this->getCompanyFromParam();

        return $this->page('company_office/add', [
            'company' => $company,
            'group_office' => arr2dto([], 'group_office'),
        ]);
    }

    public function addPost()
    {
        $company = $this->getCompanyFromParam();
        $data = $this->getRequestData();
        $pack = $this->group_office_service->create($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_office-list', [
                'gid' => $company->gid
            ]);
        }

        return $this->page('company_office/add', [
            'company' => $company,
            'group_office' => arr2dto($data, 'group_office'),
            'errors' => $pack->getErrors()
        ]);
    }

    public function edit()
    {
        $company = $this->getCompanyFromParam();
        $group_office = $this->getGroupOfficeFromParam();

        return $this->page('company_office/edit', [
            'company' => $company,
            'group_office' => $group_office
        ]);
    }

    public function editPost()
    {
        $company = $this->getCompanyFromParam();
        $data = $this->getRequestData();
        $pack = $this->group_office_service->update(['id' => $data['id']], $data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_office-list', [
                'gid' => $company->gid
            ]);
        }

        return $this->page('company_office/edit', [
            'group_office' => arr2dto($data, 'group_office'),
            'company' => $company,
            'errors' => $pack->getErrors()
        ]);
    }

    public function delete()
    {
        $company = $this->getCompanyFromParam();
        $group_office = $this->getGroupOfficeFromParam();

        return $this->page('company_office/delete', [
            'company' => $company,
            'group_office' => $group_office
        ]);
    }

    public function deletePost()
    {
        $company = $this->getCompanyFromParam();
        $group_office = $this->getGroupOfficeFromParam();
        $group_office_id = $this->request->request->get('id');
        $pack = $this->group_office_service->delete(['id' => $group_office_id]);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_office-list', [
                'gid' => $company->gid
            ]);
        }

        return $this->page('group_delete-office', [
            'group_office' => $group_office
        ]);
    }

    protected function getRequestData()
    {
        $post = $this->request->request;

        return [
            'id' => (int)$post->get('id'),
            'gid' => (int)$post->get('gid'),
            'name' => $post->get('name'),
            'office_address' => $post->get('office_address')
        ];
    }

    protected function getGroupOfficeFromParam()
    {
        $group_office_id = $this->getParam('group_office_id');
        $group_office = $this->group_office_service->findOne(['id' => $group_office_id]);

        if (!(bool)$group_office) {
            die('error-request');
        }

        return $group_office;
    }

    protected function getCompanyFromParam()
    {
        $gid = $this->getParam('gid');
        $company_service = gap_service_manager()->make('company');
        $company = $company_service->findOne(['gid' => $gid]);

        if (!(bool)$company || (get_type_key($company->type_id) != 'company')) {
            die('error-request');
        }

        return $company;
    }
}