<?php
namespace Mars\Service;

class GroupOfficeService extends \Gap\Service\ServiceBase
{
    protected $group_office_repo;

    public function bootstrap()
    {
        $this->group_office_repo = gap_repo_manager()->make('group_office');
    }

    public function search($query = [])
    {
        return $this->group_office_repo->search($query);
    }

    public function create($data)
    {
        return $this->group_office_repo->create($data);
    }

    public function update($query, $data)
    {
        return $this->group_office_repo->update($query, $data);
    }

    public function findOne($query = [], $fields = [])
    {
        return $this->group_office_repo->findOne($query, $fields);
    }

    public function delete($query)
    {
        return $this->group_office_repo->delete($query);
    }
}


