<?php
namespace Mars\Service;

class GroupSocialService extends \Gap\Service\ServiceBase
{
    protected $group_social_repo;

    public function bootstrap()
    {
        $this->group_social_repo = gap_repo_manager()->make('group_social');
    }

    public function search($query = [])
    {
        return $this->group_social_repo->search($query);
    }

    public function create($data)
    {
        return $this->group_social_repo->create($data);
    }

    public function update($query, $data)
    {
        return $this->group_social_repo->update($query, $data);
    }

    public function findOne($query = [], $fields = [])
    {
        return $this->group_social_repo->findOne($query, $fields);
    }

    public function delete($query)
    {
        return $this->group_social_repo->delete($query);
    }
}


