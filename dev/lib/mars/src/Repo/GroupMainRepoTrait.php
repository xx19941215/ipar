<?php

namespace Mars\Repo;

trait GroupMainRepoTrait
{

    public function findGroupMainByGid($gid)
    {
        return $this->db->select()
            ->from('group_main')
            ->where('gid', '=', $gid, 'int')
            ->fetchOne();
    }

    public function createGroupMain($data = [])
    {
        $gid = prop($data, 'gid', 0);
        $locale_id = prop($data, 'locale_id', 1);
        $title = prop($data, 'title', '');
        $content = prop($data, 'content', '');
        $zcode = prop($data, 'zcode', $this->generateZcode());
        // if ($this->findGroupMainByGid($gid)) {
        //     return $this->packError('gid', 'GroupMain had already exists');
        // }
        $create_group_main = $this->db->insert('group_main')
            ->value('gid', $gid, 'int')
            ->value('zcode', $zcode)
            ->value('locale_id', $locale_id, 'int')
            ->value('title', $title)
            ->value('content', $content)
            ->execute();
        if ($create_group_main) {
            return $this->packOK();
        }
        return $this->packError('group_main', 'insert-failed');
    }

    public function updateGroupMain($data = [])
    {
        $gid = prop($data, 'gid', 0);
        $locale_id = prop($data, 'locale_id', 1);
        $title = prop($data, 'title', '');
        $content = prop($data, 'content', '');
        $zcode = prop($data, 'zcode', '');

        // if (!$this->findGroupMainByGid($gid)) {
        //     return $this->packError('gid', 'GroupMain not exists');
        // }
        $update = $this->db->update('group_main')
            ->where('gid', '=', $gid, 'int');
        if ($locale_id) {
            $update->set('locale_id', $locale_id, 'int');
        }
        if ($title) {
            $update->set('title', $title);
        }
        if ($content) {
            $update->set('content', $content);
        }
        if ($zcode) {
            $update->set('zcode', $zcode);
        }
        if ($update->execute()) {
            return $this->packOk();
        }
        return $this->packError('group_main', 'update-failed');
    }

    public function deleteGroupMainByGid($query = [])
    {
        $gid = (int)prop($query, 'gid', 0);
        $locale_id = (int)prop($query, 'locale_id', 0);

        if (!$this->findGroupMainByGid($gid)) {
            return $this->packError('gid', 'not-found');
        }
        $delete = $this->db->delete()
            ->from('group_main');
        if ($gid) {
            $delete->andWhere('gid', '=', $gid, 'int');
        }
        if ($locale_id) {
            $delete->andWhere('locale_id', '=', $locale_id, 'int');
        }

        if ($delete->execute()) {
            return $this->packOk();
        }
        return $this->packError('group_main', 'delete-failed');
    }
}
