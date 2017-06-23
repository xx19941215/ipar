<?php

namespace Mars\Repo;

class GroupUserRepo extends MarsRepoBase
{
    public function schGroupUserSsb($query = [])
    {
        $id = (int)prop($query, 'id', 0);
        $gid = (int)prop($query, 'gid', 0);
        $uid = (int)prop($query, 'uid', 0);
        $ssb = $this->db->select()
            ->from('group_user')
            ->setDto('group_user');
        if ($id) {
            $ssb->andWhere('id', '=', $id, 'int');
        }
        if ($gid) {
            $ssb->andWhere('gid', '=', $gid, 'int');
        }
        if ($uid) {
            $ssb->andWhere('uid', '=', $uid, 'int');
        }
        return $ssb;
    }

    public function findGroupUser($query = [])
    {
        $ssb = $this->schGroupUserSsb($query);
        if (!$ssb->getWheres()) {
            return null;
        }
        return $ssb->fetchOne();
    }
    public function createGroupUser($data = [])
    {
        $gid = (int)prop($data, 'gid', 0);
        $uid = prop($data, 'uid', service('user')->getCurrentUid());
        $roles = prop($data, 'roles', '');
        $start = prop($data, 'start', '');
        $end = prop($data, 'end', '');

        if ($gid <= 0) {
            return $this->packError('gid', 'not-positive');
        }
        $create_group_social = $this->db->insert('group_user')
            ->value('gid', $gid, 'int')
            ->value('uid', $uid, 'int')
            ->value('roles', $roles)
            ->value('start', $start)
            ->value('end', $end)
            ->execute();
        if ($create_group_social) {
            return $this->packItem('group_user_id', $this->db->lastInsertId());
        }
        return $this->packError('group_user', 'insert-failed');

    }

    public function updateGroupUser($data)
    {
        $id = (int)prop($data, 'id', 0);
        $gid = (int)prop($data, 'gid', 0);
        $uid = prop($data, 'uid', service('user')->getCurrentUid());
        $roles = prop($data, 'roles', '');
        $start = prop($data, 'start', '');
        $end = prop($data, 'end', '');

        if (!$id) {
            return $this->packError('id', 'id empty');
        }
        if ($id <= 0) {
            return $this->packError('id', 'not-positive');
        }
        if ($gid <= 0) {
            return $this->packError('gid', 'not-positive');
        }
        if (!$this->findGroupUser($data)) {
            return $this->packError('gid', 'not find');
        }

        $ssb = $this->db->update('group_user');
        if ($id) {
            $ssb->andWhere('id', '=', $id, 'int');
        }
        if ($gid) {
            $ssb->andWhere('gid', '=', $gid, 'int');
        }
        if ($uid) {
            $ssb->set('uid', $uid, 'int');
        }
        if ($roles) {
            $ssb->set('roles', $roles);
        }
        if ($start) {
            $ssb->set('start', $start);
        }
        if ($end) {
            $ssb->set('end', $end);
        }
        if ($ssb->execute()) {
            return $this->packOk();
        }
        return $this->packError('group_user', 'update-failed');
    }

    public function deleteGroupUser($query = [])
    {
        $id = prop($query, 'id', 0);
        $gid = prop($query, 'gid', 0);

        if (!$this->findGroupUser($query)) {
            return $this->packError('gid', 'not find');
        }

        $ssb = $this->db->delete()
            ->from('group_user');
        if ($id) {
            $ssb->andWhere('id', '=', $id, 'int');
        }
        if ($gid) {
            $ssb->andWhere('gid', '=', $gid, 'int');
        }
        if ($ssb->execute()) {
            return $this->packOk();
        }
        return $this->packError('group_user', 'delete-failed');
    }

    public function schGroupUserSet($query = [])
    {
        return $this->dataSet(
            $this->schGroupUserSsb($query)
        );
    }

}
