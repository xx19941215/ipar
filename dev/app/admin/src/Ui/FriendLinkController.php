<?php

namespace Admin\Ui;

class FriendLinkController extends AdminControllerBase
{
    public function index()
    {
        $page = $this->request->query->get('page');
        $search = $this->request->query->get('query');
        $friend_link_set = $this->service('friend_link')->search(['search' => $search]);
        $friend_link_set->setCurrentPage($page);

        return $this->page('friend_link/index', [
            'friend_link_set' => $friend_link_set
        ]);
    }

    public function edit()
    {
        $friend_link_id = $this->getParam('friend_link_id');
        $friend_link = $this->service('friend_link')->findOne($friend_link_id);
        $friend_link_type_set = $this->service('friend_link')->findTypeSet();
        return $this->page('friend_link/edit', [
            'friend_link' => (array)$friend_link,
            'friend_link_type_set' => $friend_link_type_set->getItems(),
        ]);
    }


    public function add()
    {
        $friend_link_type_set = $this->service('friend_link')->findTypeSet();
        return $this->page('friend_link/add', [
            'friend_link_type_set' => $friend_link_type_set->getItems(),
        ]);
    }

    public function addPost()
    {
        $data = $this->getRequestData();
        if ($img_file = $this->request->files->get('logo_img')) {
            $data['logo_img'] = json_encode($this->uploadLogo($img_file));
        }

        $pack = $this->service('friend_link')->create($data);
        if ($pack->isOK()) {
            return $this->gotoRoute('admin-ui-friend-link-index');
        }
        $data['errors'] = $pack->getErrors();
        return $this->page('friend_link/add', $data);

    }

    public function editPost()
    {
        $data = $this->getRequestData();
        if ($img_file = $this->request->files->get('logo_img')) {
            $data['logo_img'] = json_encode($this->uploadLogo($img_file));
        }

        $pack = $this->service('friend_link')->update($data);
        if ($pack->isOK()) {
            return $this->gotoRoute('admin-ui-friend-link-index');
        }
        $data['errors'] = $pack->getErrors();
        return $this->page('friend_link/edit', $data);

    }

    public function deactivate()
    {
        $id = $this->getParam('friend_link_id');

        return $this->page('friend_link/deactivate', [
            'id' => $id
        ]);
    }

    public function deactivatePost()
    {
        $id = $this->getRequestData()['id'];
        $pack = $this->service('friend_link')->deactivate($id);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-friend-link-index');
        }

        $data['errors'] = $pack->getErrors();
        return $this->page('friend_link/deactivate', [
            'id' => $id
        ]);
    }

    public function activate()
    {
        $id = $this->getParam('friend_link_id');

        return $this->page('friend_link/activate', [
            'id' => $id
        ]);
    }

    public function activatePost()
    {
        $id = $this->getRequestData()['id'];
        $pack = $this->service('friend_link')->activate($id);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-friend-link-index');
        }

        $data['errors'] = $pack->getErrors();
        return $this->page('friend_link/activate', [
            'id' => $id
        ]);
    }

    public function getRequestData()
    {
        $post = $this->request->request;

        return [
            'id' => $post->get('id'),
            'type' => $post->get('friend-link-type'),
            'title' => $post->get('link-title'),
            'url' => $post->get('link-url')
        ];
    }

    protected function uploadLogo($img_file)
    {
        $img_tool = image_tool();
        $pack = $img_tool->save($img_file);

        if (!$pack->isOk()) {
            return $pack->toArray();
        }

        $image = $pack->getItem('image');

        return [
            'site' => config()->get('img.site'),
            'dir' => $image->dir,
            'name' => $image->name,
            'ext' => $image->ext
        ];
    }
}
