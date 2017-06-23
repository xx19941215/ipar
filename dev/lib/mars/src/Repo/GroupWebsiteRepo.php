<?php

namespace Mars\Repo;

class GroupWebsiteRepo extends MarsRepoBase
{
    public function schGroupWebsiteSsb($query = [])
    {
        $id = (int)prop($query, 'id', 0);
        $gid = (int)prop($query, 'gid', 0);
        $ssb = $this->db->select()
            ->from('group_website')
            ->setDto('group_website');
        if ($id) {
            $ssb->andWhere('id', '=', $id, 'int');
        }
        if ($gid) {
            $ssb->andWhere('gid', '=', $gid, 'int');
        }
        return $ssb;
    }

    public function findGroupWebsite($query = [])
    {
        $ssb = $this->schGroupWebsiteSsb($query);
        if (!$ssb->getWheres()) {
            return null;
        }
        return $ssb->fetchOne();
    }
    public function createGroupWebsite($data = [])
    {
        $gid = prop($data, 'gid', 0);
        $locale_id = prop($data, 'locale_id', 1);
        $url = prop($data, 'url', '');
        $title = prop($data, 'title', '');

        if (!$gid) {
            return $This->packError('gid', 'gid-empty');
        }
        $gid = (int)$gid;
        if ($gid <= 0) {
            return $this->packError('gid', 'not-positive');
        }
        $locale_id = (int)$locale_id;
        if ($locale_id <= 0) {
            return $this->packError('locale_id', 'not-positive');
        }
        if (empty($title)) {
            return $this->packError('title', 'empty');
        }
        if (empty($url)) {
            return $this->packError('url', 'empty');
        }

        $create_group_website = $this->db->insert('group_website')
            ->value('gid', $gid, 'int')
            ->value('locale_id', $locale_id, 'int')
            ->value('url', $url)
            ->value('title', $title)
            ->execute();
        if ($create_group_website) {
            return $this->packItem('group_website_id', $this->db->lastInsertId());
        }
        return $this->packError('group_website', 'insert-failed');

    }

    public function updateGroupWebsite($data)
    {
        $id = (int)prop($data, 'id', 0);
        $gid = (int)prop($data, 'gid', 0);
        $locale_id = (int)prop($data, 'locale_id', 1);
        $url = prop($data, 'url', '');
        $title = prop($data, 'title', '');

        if ($id <= 0) {
            return $this->packError('id', 'not-positive');
        }
        if ($gid <= 0) {
            return $this->packError('gid', 'not-positive');
        }
        if ($locale_id <= 0) {
            return $this->packError('locale_id', 'not-positive');
        }
        if (!$this->findGroupWebsite($data)) {
            return $this->packError('gid', 'not find');
        }
        $ssb = $this->db->update('group_website');
        if ($id) {
            $ssb->andWhere('id', '=', $id, 'int');
        }
        if ($gid) {
            $ssb->andWhere('gid', '=', $gid, 'int');
        }
        if ($locale_id) {
            $ssb->set('locale_id', $locale_id, 'int');
        }
        if ($url) {
            $ssb->set('url', $url);
        }
        if ($title) {
            $ssb->set('title', $title);
        }
        if ($ssb->execute()) {
            return $this->packOk();
        }
        return $this->packError('group_website', 'update-failed');
    }

    public function deleteGroupWebsite($query = [])
    {
        $id = (int)prop($query, 'id', 0);
        $gid = (int)prop($query, 'gid', 0);
        if ($id <= 0) {
            return $this->packError('id', 'not-positive');
        }
        if ($gid <= 0) {
            return $this->packError('gid', 'not-positive');
        }
        if (!$this->findGroupWebsite($query)) {
            return $this->packError('gid', 'not find');
        }

        $ssb = $this->db->delete()
            ->from('group_website');
        if ($id) {
            $ssb->andWhere('id', '=', $id, 'int');
        }
        if ($gid) {
            $ssb->andWhere('gid', '=', $gid, 'int');
        }
        if ($ssb->execute()) {
            return $this->packOk();
        }
        return $this->packError('group_website', 'delete-failed');
    }

    public function schGroupWebsiteSet($query = [])
    {
        return $this->dataSet(
            $this->schGroupWebsiteSsb($query)
        );
    }

}
