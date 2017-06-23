<?php
namespace Admin\Ui;

class CompanySocialController extends AdminControllerBase
{
    protected $group_social_service;

    public function bootstrap()
    {
        $this->group_social_service = gap_service_manager()->make('group_social');
    }

    public function list()
    {
        $company = $this->getCompanyFromParam();
        $group_social_set = $this->getGroupSocialSetFromParam();

        return $this->page('company_social/show', [
            'company' => $company,
            'group_social_set' => $group_social_set,
            'select' => 'social'
        ]);
    }

    public function add()
    {
        $company = $this->getCompanyFromParam();
        $group_social_set = $this->getGroupSocialSetFromParam();

        return $this->page('company_social/add', [
            'company' => $company,
            'group_social_set' => $group_social_set,
            'group_social' => arr2dto([], 'group_social'),
            'action' => 'add',
            'select' => 'social'
        ]);
    }

    public function addPost()
    {
        $company = $this->getCompanyFromParam();
        $group_social_set = $this->getGroupSocialSetFromParam();
        $data = $this->getRequestData();

        if ($this->request->files->get('qrcode')) {
            $data['qrcode'] = json_encode($this->uploadQrcode($this->request->files->get('qrcode')));
        };

        $pack = $this->group_social_service->create($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_social-add', [
                'gid' => $company->gid
            ]);
        }

        return $this->page('company_social/add', [
            'company' => $company,
            'group_social_set' => $group_social_set,
            'errors' => $pack->getErrors(),
            'group_social' => arr2dto($data, 'group_social'),
            'action' => 'add'
        ]);
    }

    public function edit()
    {
        $company = $this->getCompanyFromParam();
        $group_social = $this->getGroupSocialFromParam();
        $group_social_set = $this->getGroupSocialSetFromParam();

        return $this->page('company_social/edit', [
            'company' => $company,
            'group_social' => $group_social,
            'group_social_set' => $group_social_set
        ]);
    }

    public function editPost()
    {
        $company = $this->getCompanyFromParam();
        $group_social = $this->getGroupSocialFromParam();
        $group_social_set = $this->getGroupSocialSetFromParam();
        $data = $this->getRequestData();

        if ($this->request->files->get('qrcode')) {
            $data['qrcode'] = json_encode($this->uploadQrcode($this->request->files->get('qrcode')));
        };

        $pack = $this->group_social_service->update(['id' => $data['id']], $data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_social-list', [
                'gid' => $company->gid
            ]);
        }

        return $this->page('company_social/edit', [
            'company' => $company,
            'group_social' => $group_social,
            'group_social_set' => $group_social_set,
            'errors' => $pack->getErrors()
        ]);
    }

    public function delete()
    {
        $group_social = $this->getGroupSocialFromParam();
        $company = $this->getCompanyFromParam();

        return $this->page('company_social/delete', [
            'company' => $company,
            'group_social' => $group_social
        ]);
    }

    public function deletePost()
    {
        $company = $this->getCompanyFromParam();
        $group_social = $this->getGroupSocialFromParam();
        $group_social_id = $this->request->request->get('id');
        $pack = $this->group_social_service->delete(['id' => $group_social_id]);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-company_social-list', [
                'gid' => $company->gid
            ]);
        }

        return $this->page('company_social/delete', [
            'company' => $company,
            'group_social' => $group_social
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

    protected function getGroupSocialFromParam()
    {
        $group_social_id = $this->getParam('group_social_id');
        $group_social = $this->group_social_service->findOne(['id' => $group_social_id]);

        if (!(bool)$group_social) {
            die('error-request');
        }

        return $group_social;
    }

    protected function getGroupSocialSetFromParam()
    {
        $gid = $this->getParam('gid');
        $group_social_set = $this->group_social_service->search(['gid' => $gid]);

        return $group_social_set;
    }

    protected function getRequestData()
    {
        $post = $this->request->request;

        return [
            'id' => (int)$post->get('id'),
            'gid' => (int)$post->get('gid'),
            'social_id' => $post->get('social_id'),
            'name' => $post->get('name'),
            'url' => $post->get('url'),
        ];
    }

    protected function uploadQrcode($img_file)
    {
        $img_tool = image_tool();
        $pack = $img_tool->save($img_file);

        if (!$pack->isOk()) {
            return $pack->toArray();
        }

        $image = $pack->getItem('image');
        $image->resize('small', ['w' => 100]);
        $image->resize('large', ['w' => 600]);
        $image->resize('cover', ['w' => 200, 'h' => 123]);

        return [
            'site' => config()->get('img.site'),
            'dir' => $image->dir,
            'name' => $image->name,
            'ext' => $image->ext
        ];
    }
}

