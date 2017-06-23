<?php
namespace Mars\Service;

class GroupContactService extends \Gap\Service\ServiceBase
{
    protected $group_contact_repo;

    public function bootstrap()
    {
        $this->group_contact_repo = gap_repo_manager()->make('group_contact');
    }

    public function search($query = [])
    {
        return $this->group_contact_repo->search($query);
    }

    public function create($data)
    {
        return $this->group_contact_repo->create($data);
    }

    public function update($query, $data)
    {
        return $this->group_contact_repo->update($query, $data);
    }

    public function findOne($query = [], $fields = [])
    {
        return $this->group_contact_repo->findOne($query, $fields);
    }

    public function delete($query = [])
    {
        return $this->group_contact_repo->delete($query);
    }
}
