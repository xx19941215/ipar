<?php
namespace Mars\Service;

class IndustryTagService extends \Gap\Service\ServiceBase
{
    protected $industry_tag_repo;

    public function bootstrap()
    {
        $this->industry_tag_repo = gap_repo_manager()->make('industry_tag');
    }

    public function search($query)
    {
        return $this->industry_tag_repo->search($query);
    }

    public function save($data)
    {
        return $this->industry_tag_repo->save($data);
    }

    public function update($query, $data)
    {
        return $this->industry_tag_repo->update($query, $data);
    }

    public function findOne($query = [], $fields = [])
    {
        return $this->industry_tag_repo->findOne($query, $fields);
    }

    public function delete($query)
    {
        return $this->industry_tag_repo->delete($query);
    }

    public function updateField($query, $field, $value)
    {
        return $this->industry_tag_repo->updateField($query, $field, $value);
    }
}

