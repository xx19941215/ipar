<?php
namespace Mars\Repo;

class FriendLinkRepo extends \Gap\Repo\RepoBase
{

    public function search($query = [])
    {
        $ssb = $this->db->select()
            ->setDto('friend_link')
            ->from('friend_link');
        if ($search = prop($query, 'search', '')) {
            $ssb->where('title', 'LIKE', "%$search%");
        }

        $ssb->orderBy('changed', 'desc')->fetchAll();
        return $this->dataSet($ssb);
    }

    public function findOne($friend_link_id)
    {
        $ssb = $this->db->select()
            ->setDto('friend_link')
            ->from('friend_link')
            ->where('id', '=', $friend_link_id);

        if (!$ssb) {
            return null;
        }

        return $ssb->fetchOne();
    }

    public function findTypeSet()
    {
        $ssb = $this->db->select(['type'])
            ->setDto('friend_link')
            ->from(['friend_link', 'f'])
            ->groupBy(['f', 'type']);

        return $this->dataSet($ssb);
    }

    public function create($data)
    {
        $now = current_date();

        if (!$this->db->insert('friend_link')
            ->value('type', $data['type'])
            ->value('title', $data['title'])
            ->value('url', $data['url'])
            ->value('logo_img', $data['logo_img'])
            ->value('changed', $now)
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('friend_link_insert', 'friend_link insert failed');
        }

        $id = $this->db->lastInsertId();

        return $this->packItem('id', $id);
    }

    public function update($data)
    {
        $now = current_date();
        if($data['logo_img']) {
            if(!$this->db->update('friend_link')
                ->where('id', '=', $data['id'])
                ->set('type', $data['type'])
                ->set('title', $data['title'])
                ->set('url', $data['url'])
                ->set('logo_img', $data['logo_img'])
                ->set('changed', $now)
                ->execute()
            ) {
                $this->db->rollback();
                return $this->packError('friend_link_update', 'friend_link update failed');
            }
        }else {
            if (!$this->db->update('friend_link')
                ->where('id', '=', $data['id'])
                ->set('type', $data['type'])
                ->set('title', $data['title'])
                ->set('url', $data['url'])
                ->set('changed', $now)
                ->execute()
            ) {
                $this->db->rollback();
                return $this->packError('friend_link_update', 'friend_link update failed');
            }
        }

        return $this->packItem('id', $data['id']);
    }

    public function deactivate($id)
    {
        if (!$this->db->update('friend_link')
            ->where('id', '=', $id)
            ->set('status', 0)
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('friend_link_update', 'friend_link update failed');
        }

        return $this->packItem('id', $id);
    }

    public function activate($id)
    {
        if (!$this->db->update('friend_link')
            ->where('id', '=', $id)
            ->set('status', 1)
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('friend_link_update', 'friend_link update failed');
        }

        return $this->packItem('id', $id);
    }

    public function findSetByTpye($type)
    {
        $ssb = $this->db->select()
            ->setDto('friend_link')
            ->from('friend_link')
            ->where('type','LIKE', "%$type%")
            ->Andwhere('status', '=', 1);

        $ssb->orderBy('created', 'asc')->fetchAll();
        return $this->dataSet($ssb)->setCountPerPage(0);
    }

    public function findLinkSet()
    {
        $typeSet = $this->findTypeSet()->getItems();
        $res = [];
        foreach ($typeSet as $type)
        {
            $res[$type->type] = $this->findSetByTpye($type->type)->getItems();
        }

        return $res;
    }
}