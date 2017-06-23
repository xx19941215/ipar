<?php
namespace Mars\Service;

class EntityTagService extends \Gap\Service\ServiceBase
{
    protected $entity_tag_repo;

    public function bootstrap()
    {
        $this->entity_tag_repo = $this->repo('entity_tag');
    }

    public function save($data)
    {
        return $this->entity_tag_repo->save($data);
    }

    public function delete($data)
    {
        return $this->repo('entity_tag_table')->delete($data);
    }

    public function saveTagMultiple($data)
    {
        return $this->entity_tag_repo->saveTagMultiple($data);
    }

    public function search($data)
    {
        return $this->entity_tag_repo->search($data);
    }

}