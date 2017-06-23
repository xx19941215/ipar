<?php
namespace Mars\Service;

class GroupService extends \Gap\Service\ServiceBase
{
    protected $group_repo;

    public function bootstrap()
    {
        $this->group_repo = gap_repo_manager()->make('group');
    }

    public function search($query = [])
    {
        return $this->group_repo->search($query);
    }

    public function create($data)
    {
        return $this->group_repo->create($data);
    }

    public function update($query, $data)
    {
        return $this->group_repo->update($query, $data);
    }

    public function updateField($query, $field, $value)
    {
        return $this->group_repo->updateField($query, $field, $value);
    }

    public function findOne($query = [], $fields = [])
    {
        return $this->group_repo->findOne($query, $fields);
    }

    public function delete($query)
    {
        return $this->group_repo->delete($query);
    }
}
