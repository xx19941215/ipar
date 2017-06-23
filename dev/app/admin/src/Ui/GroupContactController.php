<?php
namespace Admin\Ui;

class GroupContactController extends AdminControllerBase
{
    protected $group_contact_service;

    public function bootstrap()
    {
        $this->group_contact_service = gap_service_manager()->make('group_contact');
    }

    public function list()
    {
        $group = $this->getGroupFromParam();
        $group_contact_set = $this->group_contact_service->search(['gid' => $group->gid]);

        return $this->page('group_contact/show', [
            'group' => $group,
            'group_contact_set' => $group_contact_set,
            'select' => 'contact'
        ]);
    }

    public function add()
    {
        $group = $this->getGroupFromParam();

        return $this->page('group_contact/add', [
            'group' => $group,
            'group_contact' => arr2dto([], 'group_contact'),
        ]);
    }

    public function addPost()
    {
        $data = $this->getRequestData();
        $pack = $this->group_contact_service->create($data);
        $group = $this->getGroupFromParam();

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group_contact-list', [
                'gid' => $group->gid
            ]);
        }

        return $this->page('group_contact/add', [
            'group' => $group,
            'group_contact' => arr2dto($data, 'group_contact'),
            'errors' => $pack->getErrors()
        ]);
    }

    public function edit()
    {
        $group = $this->getGroupFromParam();
        $group_contact = $this->getGroupContactFromParam();

        return $this->page('group_contact/edit', [
            'group' => $group,
            'group_contact' => $group_contact
        ]);
    }

    public function editPost()
    {
        $data = $this->getRequestData();
        $pack = $this->group_contact_service->update(['id' => $data['id']], $data);
        $group = $this->getGroupFromParam();

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group_contact-list', [
                'gid' => $group->gid
            ]);
        }

        return $this->page('group_contact/edit', [
            'group' => $group,
            'group_contact' => arr2dto($data, 'group_contact'),
            'errors' => $pack->getErrors()
        ]);
    }

    public function delete()
    {
        $group = $this->getGroupFromParam();
        $group_contact = $this->getGroupContactFromParam();

        return $this->page('group_contact/delete', [
            'group' => $group,
            'group_contact' => $group_contact
        ]);
    }

    public function deletePost()
    {
        $group = $this->getGroupFromParam();
        $group_contact_id = $this->request->request->get('id');
        $pack = $this->group_contact_service->delete(['id' => $group_contact_id]);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group_contact-list', [
                'gid' => $group->gid
            ]);
        }

        return $this->page('group_contact/delete', [
            'group' => $group,
            'errors' => $pack->getErrors()
        ]);
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

    protected function getGroupFromParam()
    {
        $gid = $this->getParam('gid');
        $group_service = gap_service_manager()->make('group');
        $group = $group_service->findOne(['gid' => $gid]);

        if (!$group || ($group->type_id != get_type_id('group'))) {
            die('error request');
        }

        return $group;
    }

    protected function getGroupContactFromParam()
    {
        $group_contact_id = $this->getParam('group_contact_id');
        $group_contact = $this->group_contact_service->findOne(['id' => $group_contact_id]);

        return $group_contact;
    }
}
