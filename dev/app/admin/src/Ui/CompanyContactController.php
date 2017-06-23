<?php
namespace Admin\Ui;

class CompanyContactController extends AdminControllerBase
{
    protected $group_contact_service;


    public function bootstrap()
    {
        $this->group_contact_service = gap_service_manager()->make('group_contact');
    }

    public function list()
    {
        $company = $this->getCompanyFromParam();
        $group_contact_set = $this->group_contact_service->search(['gid' => $company->gid]);

        return $this->page('company_contact/show', [
            'company' => $company,
            'group_contact_set' => $group_contact_set,
            'select' => 'contact'
        ]);
    }

    public function add()
    {
        $company = $this->getCompanyFromParam();

        return $this->page('company_contact/add', [
            'company' => $company,
            'group_contact' => arr2dto([], 'group_contact'),
        ]);
    }

    public function addPost()
    {
        $data = $this->getRequestData();
        $pack = $this->group_contact_service->create($data);
        $company = $this->getCompanyFromParam();

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_contact-list', [
                'gid' => $company->gid
            ]);
        }

        return $this->page('company_contact/add', [
            'company' => $company,
            'group_contact' => arr2dto($data, 'group_contact'),
            'errors' => $pack->getErrors()
        ]);
    }


    public function edit()
    {
        $company = $this->getCompanyFromParam();
        $group_contact = $this->getGroupContactFromParam();

        return $this->page('company_contact/edit', [
            'group_contact' => $group_contact,
            'company' => $company,
        ]);
    }

    public function editPost()
    {
        $data = $this->getRequestData();
        $pack = $this->group_contact_service->update(['id' => $data['id']], $data);
        $company = $this->getCompanyFromParam();

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_contact-list', [
                'gid' => $company->gid
            ]);
        }

        return $this->page('company_contact/edit', [
            'group_contact' => arr2dto($data, 'group_contact'),
            'company' => $company,
            'errors' => $pack->getErrors()
        ]);
    }

    public function delete()
    {
        $company = $this->getCompanyFromParam();
        $group_contact = $this->getGroupContactFromParam();

        return $this->page('company_contact/delete', [
            'company' => $company,
            'group_contact' => $group_contact,
        ]);
    }

    public function deletePost()
    {
        $company = $this->getCompanyFromParam();
        $group_contact_id = $this->request->request->get('id');
        $pack = $this->group_contact_service->delete(['id' => $group_contact_id]);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_contact-list', [
                'gid' => $company->gid
            ]);
        }

        return $this->page('company_contact/delete', [
            'company' => $company,
            'errors' => $pack->getErrors()
        ]);
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


    protected function getGroupContactFromParam()
    {
        $group_contact_id = $this->getParam('group_contact_id');
        $group_contact = $this->group_contact_service->findOne(['id' => $group_contact_id]);

        return $group_contact;
    }

    protected function getRequestData()
    {
        $post = $this->request->request;

        return [
            'id' => (int)$post->get('id'),
            'gid' => (int)$post->get('gid'),
            'name' => $post->get('name'),
            'roles' => $post->get('roles'),
            'phone' => $post->get('phone'),
            'email' => $post->get('email')
        ];
    }
}