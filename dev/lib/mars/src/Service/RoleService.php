<?php
namespace Mars\Service;

class RoleService extends MarsServiceBase {

    public function createRole($title, $content = '')
    {
        if (!is_string($title) || empty($title)) {
            return $this->packNotEmpty('title');
        }
        $title_length = mb_strlen($title);
        if ($title_length < 3 || $title_length > 21) {
            return $this->packOutLength('title', 3, 21);
        }

        $role_repo = $this->repo('role');

        if ($role_repo->titleExists($title)) {
            return $this->packExists('title');
        }
        return $role_repo->createRole($title, $content);
    }

    public function updateRole($id, $title, $content = '')
    {
        $id = (int) $id;
        if (!$id) {
            return $this->packError('id', 'must-be-positive-integer');
        }

        if (!is_string($title) || empty($title)) {
            return $this->packNotEmpty('title');
        }
        $title_length = mb_strlen($title);
        if ($title_length < 3 || $title_length > 21) {
            return $this->packOutLength('title', 3, 21);
        }

        $role_repo = $this->repo('role');

        if ($role_repo->titleExists($title, $id)) {
            return $this->packExists('title');
        }
        return $role_repo->updateRole($id, $title, $content);
    }

    public function activate($role_id)
    {
        $role_id = (int) $role_id;
        if (!$role_id) {
            return $this->packError('role_id', 'must-be-positive-integer');
        }
        $u_roles = $this->repo('user')->getUidByRoleId($role_id);
        foreach($u_roles as $u_role) {
            $this->deleteCachedUser($u_role->uid);
        }
        return $this->repo('role')->activate($role_id);
    }

    public function deactivate($role_id)
    {
        $role_id = (int) $role_id;
        if (!$role_id) {
            return $this->packError('role_id', 'must-be-positive-integer');
        }
        $u_roles = $this->repo('user')->getUidByRoleId($role_id);
        foreach($u_roles as $u_role) {
            $this->deleteCachedUser($u_role->uid);
        }
        return $this->repo('role')->deactivate($role_id);
    }

    public function delete($role_id)
    {
        $role_id = (int) $role_id;
        if (!$role_id) {
            return $this->packError('role_id', 'must-be-positive-integer');
        }
        $u_roles = $this->repo('user')->getUidByRoleId($role_id);
        foreach($u_roles as $u_role) {
            $this->deleteCachedUser($u_role->uid);
        }
        return $this->repo('role')->delete($role_id);
    }

    //
    // sc-get
    //
    public function getRolesAll()
    {
        return $this->repo('role')->getRolesAll();
    }
    public function getRolesActive(){
      return $this->repo('role')->getRolesActive();
    }

    /*
        todo no role repo
    public function getRoles()
    {
        return $this->dataSet(
            $this->repo('role')->buildRoles()
        );
    }
     */

    public function getRoleById($role_id)
    {
        $role_id = (int) $role_id;
        if (!$role_id) {
            return null;
        }
        return $this->repo('role')->getRoleById($role_id);
    }
}
