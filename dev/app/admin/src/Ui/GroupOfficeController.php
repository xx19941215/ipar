<?php
namespace Admin\Ui;

class GroupOfficeController extends AdminControllerBase
{
    protected $group_office_service;

    public function bootstrap()
    {
        $this->group_office_service = gap_service_manager()->make('group_office');
    }


    public function list()
    {
        $group = $this->getGroupFromParam();
        $group_office_set = $this->group_office_service->search(['gid' => $group->gid]);

        return $this->page('group_office/show', [
            'group' => $group,
            'group_office_set' => $group_office_set,
            'select' => 'office'
        ]);
    }

    public function add()
    {
        $group = $this->getGroupFromParam();

        return $this->page('group_office/add', [
            'group' => $group,
            'group_office' => arr2dto([], 'group_office'),
        ]);
    }

    public function addPost()
    {
        $group = $this->getGroupFromParam();
        $data = $this->getRequestData();
        $pack = $this->group_office_service->create($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group_office-list', [
                'gid' => $group->gid
            ]);
        }

        return $this->page('group_office/add', [
            'group' => $group,
            'group_office' => arr2dto($data, 'group_office'),
            'errors' => $pack->getErrors()
        ]);
    }

    public function edit()
    {
        $group = $this->getGroupFromParam();
        $group_office = $this->getGroupOfficeFromParam();

        return $this->page('group_office/edit', [
            'group' => $group,
            'group_office' => $group_office
        ]);
    }

    public function editPost()
    {
        $group = $this->getGroupFromParam();
        $data = $this->getRequestData();
        $pack = $this->group_office_service->update(['id' => $data['id']], $data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group_office-list', [
                'gid' => $group->gid
            ]);
        }

        return $this->page('group_office/edit', [
            'group' => $group,
            'group_office' => arr2dto($data, 'group_office'),
            'errors' => $pack->getErrors()
        ]);
    }

    public function delete()
    {
        $group = $this->getGroupFromParam();
        $group_office = $this->getGroupOfficeFromParam();

        return $this->page('group_office/delete', [
            'group' => $group,
            'group_office' => $group_office
        ]);
    }

    public function deletePost()
    {
        $group = $this->getGroupFromParam();
        $group_office = $this->getGroupOfficeFromParam();
        $group_office_id = $this->request->request->get('id');
        $pack = $this->group_office_service->delete(['id' => $group_office_id]);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group_office-list', [
                'gid' => $group->gid
            ]);
        }

        return $this->page('group_office/delete', [
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

    protected function getGroupFromParam()
    {
        $gid = $this->getParam('gid');
        $group_service = gap_service_manager()->make('group');
        $group = $group_service->findOne(['gid' => $gid]);

        if (!(bool)$group || (get_type_key($group->type_id) != 'group')) {
            die('error-request');
        }

        return $group;
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
}
