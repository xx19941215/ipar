<?php
namespace Mars\Service;

class GroupUserService extends MarsServiceBase
{
    public function createGroupUser($data = [])
    {
        $validate = $this->validate($data);
        if (!$validate->isOk()) {
            return $validate;
        }
        $gid = prop($data, 'gid', '');
        if (!service('company')->findCompanyByGid($gid)->ok) {
            return $this->packError('gid', 'gid is not exists');
        }
        $uid = 11;//service('user')->getCurrentUid();
        return $this->repo('group_user')->createGroupUser($data);
    }

    public function updateGroupUser($data = [])
    {
        $validate = $this->validate($data);
        if (!$validate->isOk()) {
            return $validate;
        }
        return $this->repo('group_user')->updateGroupUser($data);
    }

    public function schGroupUserSet($query = [])
    {
        return $this->repo('group_user')->schGroupUserSet($query);
    }

    public function deleteGroupUser($query = [])
    {
        return $this->repo('group_user')->deleteGroupUser($query);
    }
    public function validate($data = [])
    {
        $gid = prop($data, 'gid', 0);
        $uid = prop($data, 'uid', 0);
        $roles = prop($data, 'roles', '');
        $start = prop($data, 'start', '');
        $end = prop($data, 'end', '');

        if ($gid == 0 || !is_numeric($gid)) {
            return $this->packError('gid', 'gid-error');
        }
        if (!is_numeric($uid)) {
            return $this->packError('uid', 'uid-is-not numeric');
        }
        if (!$roles) {
            return $this->packError('roles', 'roles-error');
        }
        $date = '/^(\d{4})-(0?\d{1}|1[0-2])-(0?\d{1}|[12]\d{1}|3[01])$/';
        if (!preg_match($date, $start)) {
            return $this->packError('start', 'start-date-error'.$start);
        }
        if (!preg_match($date, $end)) {
            return $this->packError('end', 'end-date-error');
        }
        return $this->packOk();
    }
}
