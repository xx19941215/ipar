<?php
namespace Admin\Ui;

class GroupSocialController extends AdminControllerBase
{
    protected $group_social_service;

    public function bootstrap()
    {
        $this->group_social_service = gap_service_manager()->make('group_social');
    }

    public function list()
    {
        $group = $this->getGroupFromParam();
        $group_social_set = $this->getGroupSocialSetFromParam();

        return $this->page('group_social/show', [
            'group' => $group,
            'group_social_set' => $group_social_set,
            'select' => 'social'
        ]);
    }

    public function add()
    {
        $group = $this->getGroupFromParam();
        $group_social_set = $this->getGroupSocialSetFromParam();

        return $this->page('group_social/add', [
            'group' => $group,
            'group_social_set' => $group_social_set,
            'group_social' => arr2dto([], 'group_social'),
            'action' => 'add',
            'select' => 'social'
        ]);
    }

    public function addPost()
    {
        $group = $this->getGroupFromParam();
        $group_social_set = $this->getGroupSocialSetFromParam();
        $data = $this->getRequestData();

        if ($this->request->files->get('qrcode')) {
            $data['qrcode'] = json_encode($this->uploadQrcode($this->request->files->get('qrcode')));
        };

        $pack = $this->group_social_service->create($data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group_social-add', [
                'gid' => $group->gid
            ]);
        }

        return $this->page('group_social/add', [
            'group' => $group,
            'group_social_set' => $group_social_set,
            'errors' => $pack->getErrors(),
            'group_social' => arr2dto($data, 'group_social'),
            'action' => 'add'
        ]);
    }

    public function edit()
    {
        $group = $this->getGroupFromParam();
        $group_social = $this->getGroupSocialFromParam();
        $group_social_set = $this->getGroupSocialSetFromParam();

        return $this->page('group_social/edit', [
            'group' => $group,
            'group_social_set' => $group_social_set,
            'group_social' => $group_social
        ]);
    }

    public function editPost()
    {
        $group = $this->getGroupFromParam();
        $group_social = $this->getGroupSocialFromParam();
        $group_social_set = $this->getGroupSocialSetFromParam();
        $data = $this->getRequestData();

        if ($this->request->files->get('qrcode')) {
            $data['qrcode'] = json_encode($this->uploadQrcode($this->request->files->get('qrcode')));
        };

        $pack = $this->group_social_service->update(['id' => $data['id']], $data);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group_social-list', [
                'gid' => $group->gid
            ]);
        }

        return $this->page('group_social/edit', [
            'group' => $group,
            'group_social' => $group_social,
            'group_social_set' => $group_social_set,
            'errors' => $pack->getErrors()
        ]);
    }

    public function delete()
    {
        $group_social = $this->getGroupSocialFromParam();
        $group = $this->getGroupFromParam();

        return $this->page('group_social/delete', [
            'group' => $group,
            'group_social' => $group_social
        ]);
    }

    public function deletePost()
    {
        $group = $this->getGroupFromParam();
        $group_social = $this->getGroupSocialFromParam();
        $group_social_id = $this->request->request->get('id');
        $pack = $this->group_social_service->delete(['id' => $group_social_id]);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-group_social-list', [
                'gid' => $group->gid
            ]);
        }

        return $this->page('group_social/delete', [
            'group' => $group,
            'group_social' => $group_social
        ]);
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
            'qrcode' => $post->get('qrcode')
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

