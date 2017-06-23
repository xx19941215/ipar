<?php

namespace Mars\Repo;

class AddressRepo extends MarsRepoBase
{
    public function fetchAreas($query = [])
    {
        $id = prop($query, 'id', 0);
        $locale_id = prop($query, 'locale_id', 0);
        $parent_id = prop($query, 'parent_id', 0);
        $type = prop($query, 'type', 0);
        $title = prop($query, 'title', '');
        $title = str_replace(" ", "", $title);
        $title = str_replace("省", "", $title);
        $title = str_replace("市", "", $title);
        $title = str_replace("区", "", $title);
        $title = explode(",", $title);
        $db = $this->db->select()
            ->from('area')
            ->setDto('area');
        if ($id) {
            $db->andWhere('id', '=', $id, 'int');
        }
        if ($locale_id) {
            $db->andWhere('locale_id', '=', $locale_id, 'int');
        }
        if ($parent_id && count($title) <=2) {
            $db->andWhere('parent_id', '=', $parent_id, 'int');
        }
        if ($type) {
            $db->andWhere('type', '=', $type, 'int');
        }
        if ($title) {
            $match_title = "%";
            foreach ($title as $key => $value) {
                $match_title .= $value."%";
            }
            $db->andWhere('title', 'LIKE', $match_title);
        }

        $areas = $db->fetchAll();
        if ($areas) {
            return $this->packItem('areas', $areas);
        }

        if (count($title) > 2) {
            $addArea = $this->createArea([
                    'locale_id' => $locale_id,
                    'parent_id' => $parent_id,
                    'title' => $title
                ]);
            if ($addArea->isOk()) {
                return $this->packItem('area', [ ['id' => $addArea->getItem('area_id')] ]);
            } else {
                return $addArea;
            }
        }

        return $this->packError('area', 'not-found');
        
    }

    public function findAddressById($id)
    {
        $find = $this->db->select(['address', '*'], ['area', 'title'])
            ->from(['address', 'address'])
            ->leftjoin(
                ['area', 'area'],
                ['address', 'area_id'],
                '=',
                ['area', 'id']
            )
            ->where(['address', 'id'], '=', $id, '')
            ->fetchOne();
        if ($find) {
            return $find;
        }
        return $this->packError('address', 'id-not-find');
    }

    public function createArea($data = [])
    {
        $locale_id = prop($data, 'locale_id', 1);
        $parent_id = prop($data, 'parent_id', 0);
        $title = prop($data, 'title', '');

        if ($locale_id == 0) {
            $locale_id = 1;
        }
        if ($parent_id == 0) {
            return $this->packError('parent_id', 'empty');
        }
        if (!$title) {
            return $this->packError('title', 'empty');
        }
        $parent = $this->fetchAreas(['parent_id' => $parent_id, 'title' => $title[1]]);
        if (!$parent->isOk()) {
            return $this->packError('parent_id', 'not-exists');
        }

        $title = $parent->getItem('area')[0]->title.",".$title[2];
        $parent_id = $parent->getItem('area')[0]->id;

        $create = $this->db->insert('area')
            ->value('locale_id', $locale_id, 'int')
            ->value('parent_id', $parent_id, 'int')
            ->value('title', $title)
            ->value('type', 3, 'int')
            ->execute();
        if ($create) {
            return $this->packItem('area_id', $this->db->lastInsertId());
        }
        return $this->packError('area', 'insert-failed');
    }

    public function createAddress($data = [])
    {
        $locale_id = prop($data, 'locale_id', 1);
        $area_id = prop($data, 'area_id', 0);
        $title = prop($data, 'title', '');
        if (empty($title)) {
            return $this->packError('title', 'empty');
        }
        $create = $this->db->insert('address')
            ->value('locale_id', $locale_id, 'int')
            ->value('area_id', $area_id, 'int')
            ->value('title', $title)
            ->execute();
        if ($create) {
            return $this->packItem('address_id', $this->db->lastInsertId());
        }
        return $this->packError('address', 'insert-failed');
    }

    public function updateAddress($data = [])
    {
        $id = prop($data, 'id', 0);
        $locale_id = prop($data, 'locale_id', 1);
        $area_id = prop($data, 'area_id', 0);
        $title = prop($data, 'title', '');

        if (!$id) {
            return $this->packError('id', 'empty');
        }
        $update = $this->db->update('address')
            ->where('id', '=', $id, 'int');
        if ($locale_id) {
            $update->set('locale_id', $locale_id, 'int');
        }
        if ($area_id) {
            $update->set('area_id', $area_id, 'int');
        }
        if ($title) {
            $update->set('title', $title);
        }
        $update = $update->execute();
        if ($update) {
            return $this->packOk();
        }
        return $update;
    }
}
