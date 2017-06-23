<?php
namespace Mars\Service;

class FriendLinkService extends \Gap\Service\ServiceBase
{

    public function search($query = [])
    {
        return $this->repo('friend_link')->search($query);
    }

    public function findOne($friend_link_id)
    {
        return $this->repo('friend_link')->findOne($friend_link_id);
    }

    public function findTypeSet()
    {
        return $this->repo('friend_link')->findTypeSet();
    }
    public function create($data)
    {
        return $this->repo('friend_link')->create($data);
    }

    public function update($data)
    {
        return $this->repo('friend_link')->update($data);
    }

    public function deactivate($id)
    {
        return $this->repo('friend_link')->deactivate($id);
    }

    public function activate($id)
    {
        return $this->repo('friend_link')->activate($id);
    }

    public function findLinkSet()
    {
        return $this->repo('friend_link')->findLinkSet();
    }
}

