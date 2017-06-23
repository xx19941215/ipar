<?php
namespace Mars\Service;

class BrandTagService extends \Gap\Service\ServiceBase
{
    protected $brand_tag_repo;

    public function bootstrap()
    {
        $this->brand_tag_repo = gap_repo_manager()->make('brand_tag');
    }
    public function search($query)
    {
        return $this->brand_tag_repo->search($query);
    }
    public function findOne($query = [], $fields = [])
    {
        return $this->brand_tag_repo->findOne($query, $fields);
    }

    public function save($data)
    {
        return $this->brand_tag_repo->save($data);
    }
}

