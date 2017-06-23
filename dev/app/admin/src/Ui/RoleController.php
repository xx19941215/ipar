<?php
namespace Admin\Ui;

class RoleController extends AdminControllerBase
{
    public function index()
    {
        return $this->page('role/index', [
            'roles' => $this->service('role')->getRoles()
        ]);
    }

    public function search()
    {
        return $this->page('role/search');
    }

    public function show()
    {
        return $this->page('role/show');
    }

    public function add()
    {
        return $this->page('role/add');
    }

    public function addPost()
    {
        $post = $this->request->request;
        $pack = $this->service('role')->createRole(
            $post->get('title'),
            $post->get('content')
        );
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-role');
        } else {
            $data = $post->all();
            $data['errors'] = $pack->getErrors();
            return $this->page('role/add', $data);
        }
    }

    public function edit()
    {
        $role = $this->getRoleFromParam();
        return $this->page('role/edit', [
            'id' => $role->id,
            'title' => $role->title,
            'content' => $role->content,
        ]);
    }

    public function editPost()
    {
        $param_role_id = $this->getParam('role_id');
        $post = $this->request->request;
        $id = $post->get('id');
        $title = $post->get('title');
        $content = $post->get('content');
        if ($id != $param_role_id) {
            die('error request');
        }
        $pack = $this->service('role')->updateRole($id, $title, $content);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-role');
        } else {
            return $this->page('role/edit', [
                'id' => $id,
                'title' => $title,
                'content' => $content
            ]);
        }
    }

    public function activate()
    {
        return $this->page('role/activate', [
            'role' => $this->getRoleFromParam()
        ]);
    }

    public function activatePost()
    {
        $param_role_id = $this->getParam('role_id');
        $post_role_id = $this->request->request->get('role_id');
        if ($param_role_id != $post_role_id) {
            die('error request');
        }
        $pack = $this->service('role')->activate($post_role_id);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-role');
        } else {
            return $this->page('role/activate', [
                'role' => $this->getRoleFromParam(),
                'errors' => $pack->getErrors()
            ]);
        }
    }

    public function deactivate()
    {
        return $this->page('role/deactivate', [
            'role' => $this->getRoleFromParam()
        ]);
    }

    public function deactivatePost()
    {
        $param_role_id = $this->getParam('role_id');
        $post_role_id = $this->request->request->get('role_id');
        if ($param_role_id != $post_role_id) {
            die('error request');
        }
        $pack = $this->service('role')->deactivate($post_role_id);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-role');
        } else {
            return $this->page('role/deactivate', [
                'role' => $this->getRoleFromParam(),
                'errors' => $pack->getErrors()
            ]);
        }
    }

    public function delete()
    {
        return $this->page('role/delete', [
            'role' => $this->getRoleFromParam()
        ]);
    }

    public function deletePost()
    {
        $param_role_id = $this->getParam('role_id');
        $post_role_id = $this->request->request->get('role_id');
        if ($param_role_id != $post_role_id) {
            die('error request');
        }
        $pack = $this->service('role')->delete($post_role_id);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-role');
        } else {
            return $this->page('role/delete', [
                'role' => $this->getRoleFromParam(),
                'errors' => $pack->getErrors()
            ]);
        }
    }

    protected function getRoleFromParam()
    {
        if ($role_id = $this->getParam('role_id')) {
            return $this->service('role')->getRoleById($role_id);
        } else {
            return null;
        }
    }
}
