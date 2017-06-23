<?php

namespace Admin\Ui;

class UserController extends AdminControllerBase
{
    protected $user_service;

    public function bootstrap()
    {
        $this->user_service = user_service();
    }

    public function index()
    {
        $query = $this->request->query->get('query');

        $user_set = $this->user_service->schUserSet([
            'query' => $query,
        ]);
        $user_set->setCurrentPage($this->request->query->get('page'));

        return $this->page('user/index', [
            'user_set' => $user_set,
        ]);
    }

    public function search()
    {
        return $this->page('user/search');
    }

    public function show()
    {
        $user = $this->getUserFromParam();

        return $this->page('user/show', [
            'user' => $user,
        ]);
    }

    public function activate()
    {
        $user = $this->getUserFromParam();

        return $this->page('user/activate', [
            'user' => $user,
        ]);
    }

    public function activatePost()
    {
        $param_uid = $this->getParam('uid');
        $post_uid = $this->request->request->get('uid');
        if ($param_uid != $post_uid) {
            die('error request');
        }
        $pack = $this->user_service->activateUser(['uid' => $post_uid]);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-user');
        } else {
            return $this->page('user/activate', [
                'user' => $this->getUserFromParam(),
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function deactivate()
    {
        $user = $this->getUserFromParam();

        return $this->page('user/deactivate', [
            'user' => $user,
        ]);
    }

    public function deactivatePost()
    {
        $param_uid = $this->getParam('uid');
        $post_uid = $this->request->request->get('uid');
        if ($param_uid != $post_uid) {
            die('error request');
        }
        $pack = $this->user_service->deactivateUser(['uid' => $post_uid]);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-user');
        } else {
            return $this->page('user/deactivate', [
                'user' => $this->getUserFromParam(),
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function block()
    {
        return $this->page('user/block');
    }

    public function blockPost()
    {
        return $this->page('user/block');
    }

    public function delete()
    {
        $user = $this->getUserFromParam();
        if ($user->status != 0) {
            die('user must have been deacivated');
        }

        return $this->page('user/delete', [
            'user' => $user,
        ]);
    }

    public function deletePost()
    {
        $param_uid = $this->getParam('uid');
        $post_uid = $this->request->request->get('uid');
        if ($param_uid != $post_uid) {
            die('error request');
        }
        $pack = $this->user_service->deleteUserByUid($post_uid);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-user');
        } else {
            return $this->page('user/delete', [
                'user' => $this->getUserFromParam(),
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function assignPrivilege()
    {
        return $this->page('user/assign-privilege', [
            'user' => $this->getUserFromParam(),
        ]);
    }

    public function assignPrivilegePost()
    {
        $param_uid = $this->getParam('uid');
        $post_uid = $this->request->request->get('uid');
        if ($param_uid != $post_uid) {
            die('error request');
        }
        $pack = $this->user_service->assignPrivilege($post_uid, $this->request->request->get('privilege'));
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-user');
        } else {
            return $this->page('user/assign-privilege', [
                'user' => $this->getUserFromParam(),
                'errors' => $pack->getErrors(),
            ]);
        }
    }


    public function assignRole()
    {
        return $this->page('user/assign-role', [
            'user' => $this->getUserFromParam(),
            'roles' => $this->service('role')->getRolesActive(),
        ]);
    }

    public function assignRolePost()
    {
        $param_uid = $this->getParam('uid');
        $post_uid = $this->request->request->get('uid');
        $roleIds = $this->request->request->get('role_id');
        if ($param_uid != $post_uid) {
            die('error request');
        }

        $pack = $this->user_service->cancelRole($post_uid);
        if($roleIds) {
            foreach($roleIds as $role_id) {
                $pack = $this->user_service->assignRole($post_uid, $role_id);
            }
        }

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-user');
        } else {
            return $this->page('user/assign-role', [
                'user' => $this->getUserFromParam(),
                'roles' => $this->service('role')->getRolesActive(),
                'errors' => $pack->getErrors(),
            ]);
        }
    }


    protected function getUserFromParam()
    {
        if ($uid = $this->getParam('uid')) {
            return $this->user_service->getUserByUid($uid);
        } else {
            return;
        }
    }
}
