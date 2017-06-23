<?php

namespace Admin\Ui;

class CompanyController extends AdminControllerBase
{

    protected $company_service;

    public function bootstrap()
    {
        $this->company_service = gap_service_manager()->make('company');
    }

    public function show()
    {
        $company = $this->getCompanyFromParam();

        return $this->page('company/show', [
            'company' => $company,
            'select' => 'description'
        ]);
    }

    public function add()
    {
        return $this->page('company/add', [
            'action' => 'add',
            'company' => arr2dto([], 'company')
        ]);

    }

    public function addPost()
    {
        $data = $this->getRequestData();
        $pack = $this->company_service->create($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company-show', [
                'gid' => $pack->getItem('id')
            ]);
        }

        return $this->page('company/add', [
            'action' => 'add',
            'company' => arr2dto($data, 'company'),
            'errors' => $pack->getErrors()
        ]);
    }


    public function edit()
    {
        $company = $this->getCompanyFromParam();

        return $this->page('company/edit', [
            'company' => $company
        ]);
    }

    public function editPost()
    {
        $data = $this->getRequestData();
        $pack = $this->company_service->update(['gid' => $data['gid']], $data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company-show', [
                'gid' => $data['gid']
            ]);
        }

        return $this->page('company/edit', [
            'company' => arr2dto($data, 'company'),
            'errors' => $pack->getErrors()
        ]);

    }

    public function deactivate()
    {
        $company = $this->getCompanyFromParam();

        return $this->page('group/deactivate', [
            'company' => $company
        ]);
    }

    public function deactivatePost()
    {
        $gid = $this->request->request->get('gid');
        $company = $this->getCompanyFromParam();
        $status = 0;
        $pack = $this->company_service->updateField(['gid' => $gid], 'status', $status);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group-index');
        }

        return $this->page('group/deactivate', [
            'company' => $company,
            'errors' => $pack->getErrors()
        ]);
    }

    public function activate()
    {
        $company = $this->getCompanyFromParam();

        return $this->page('group/activate', [
            'company' => $company
        ]);
    }

    public function activatePost()
    {
        $gid = $this->request->request->get('gid');
        $company = $this->getCompanyFromParam();
        $status = 1;
        $pack = $this->company_service->updateField(['gid' => $gid], 'status', $status);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company-show', [
                'gid' => $gid
            ]);
        }

        return $this->page('group/activate', [
            'company' => $company,
            'errors' => $pack->getErrors()
        ]);
    }

    public function delete()
    {
        $company = $this->getCompanyFromParam();

        return $this->page('group/delete', [
            'company' => $company
        ]);
    }

    public function deletePost()
    {
        $company = $this->getCompanyFromParam();

        if ($company->status != 0) {
            return $this->gotoRoute('admin-ui-company-show', [
                'gid' => $company->gid
            ]);
        }

        $gid = $this->request->request->get('gid');
        $pack = $this->company_service->delete($gid);

        if ($pack) {
            return $this->gotoRoute('admin-ui-group-index');
        }

        return $this->gotoRoute('admin-ui-company-show', [
            'gid' => $company->gid,
            'errors' => $pack->getErrors()
        ]);

    }

    protected function getRequestData()
    {
        $post = $this->request->request;

        return [
            'gid' => (int)$post->get('gid'),
            'name' => $post->get('name'),
            'fullname' => $post->get('fullname'),
            'content' => $post->get('content'),
            'logo' => $post->get('logo'),
            'website' => $post->get('website'),
            'size_range_id' => $post->get('size_range_id'),
            'established' => $post->get('established'),
            'uid' => $post->get('uid'),
            'type_id' => $post->get('type_id'),
            'status' => $post->get('status'),
//            'group_id' => $post->get('group_id'),
            'legal_person' => $post->get('legal_person'),
            'email' => $post->get('email'),
            'reg_address' => $post->get('reg_address'),
            'company_address' => $post->get('company_address'),
            'admin_uid' => $post->get('admin_uid'),
            'is_claimed' => $post->get('is_claimed'),
        ];
    }


    protected function getCompanyFromParam()
    {
        $gid = $this->getParam('gid');
        $company = $this->company_service->findOne(['gid' => $gid]);
        if (!(bool)$company || (get_type_key($company->type_id) != 'company')) {
            die('error-request');
        }

        return $company;
    }
}
