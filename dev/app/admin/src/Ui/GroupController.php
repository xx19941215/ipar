<?php
namespace Admin\Ui;

class GroupController extends AdminControllerBase
{
    protected $group_service;

    public function bootstrap()
    {
        $this->group_service = gap_service_manager()->make('group');
    }

    public function index()
    {
        $page = $this->request->query->get('page');
        $search = $this->request->query->get('s');
        $type_id = $this->request->query->get('type_id');
        $group_set = $this->group_service->search(['search' => $search, 'type_id' => $type_id]);
        $group_set->setCurrentPage($page);

        return $this->page('group/index', [
            'group_set' => $group_set,
        ]);
    }

    public function show()
    {
        $group = $this->getGroupFromParam();

        return $this->page('group/show', [
            'group' => $group,
            'select' => 'description'
        ]);
    }

    public function add()
    {
        return $this->page('group/add', [
            'group' => arr2dto([], 'group'),
            'action' => 'add'
        ]);
    }

    public function addPost()
    {
        $data = $this->getRequestData();
        $pack = $this->group_service->create($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group-show', [
                'gid' => $pack->getItem('id'),
            ]);
        }

        return $this->page('group/add', [
            'group' => arr2dto($data, 'group'),
            'action' => 'add',
            'errors' => $pack->getErrors()
        ]);
    }

    public function edit()
    {
        $group = $this->getGroupFromParam();

        return $this->page('group/edit', [
            'group' => $group
        ]);
    }

    public function editPost()
    {
        $gid = $this->getParam('gid');
        $post_id = $this->request->request->get('gid');

        if ($gid != $post_id) {
            die('error request');
        }

        $data = $this->getRequestData();
        $pack = $this->group_service->update(['gid' => $gid], $data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group-show', [
                'gid' => $gid
            ]);
        }

        return $this->page('group/edit', [
            'group' => arr2dto($data, 'group'),
            'errors' => $pack->getErrors()
        ]);
    }

    public function deactivate()
    {
        $group = $this->getGroupFromParam();

        return $this->page('group/deactivate', [
            'group' => $group,
        ]);
    }

    public function deactivatePost()
    {
        $gid = $this->request->request->get('gid');
        $group = $this->getGroupFromParam();
        $status = 0;
        $pack = $this->group_service->updateField(['gid' => $gid], 'status', $status);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group-index');
        }

        return $this->page('group/deactivate', [
            'group' => $group,
            'errors' => $pack->getErrors()]);
    }

    public function activate()
    {
        $group = $this->getGroupFromParam();

        return $this->page('group/activate', [
            'group' => $group,
        ]);
    }

    public function activatePost()
    {
        $gid = $this->request->request->get('gid');
        $group = $this->getGroupFromParam();
        $status = 1;
        $pack = $this->group_service->updateField(['gid' => $gid], 'status', $status);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group-show', [
                'gid' => $gid
            ]);
        }

        return $this->page('group/activate', [
            'group' => $group,
            'errors' => $pack->getErrors()
        ]);
    }

    public function delete()
    {
        $group = $this->getGroupFromParam();

        return $this->page('group/delete', [
            'group' => $group,
        ]);
    }


    public function deletePost()
    {
        $group = $this->getGroupFromParam();
        $gid = $this->request->request->get('gid');

        if ($group->status != 0) {
            return $this->gotoRoute('admin-ui-group-show', [
                'gid' => $group->gid
            ]);
        }

        $pack = $this->group_service->delete(['gid' => $gid]);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group-index');
        }

        return $this->gotoRoute('admin-ui-group-show', [
            'gid' => $group->gid
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
        ];
    }

    protected function getGroupFromParam()
    {
        $gid = $this->getParam('gid');
        $group = $this->service('group')->findOne(['gid' => $gid]);

        if (!$group || ($group->type_id != get_type_id('group'))) {
            die('error request');
        }

        return $group;
    }
}
