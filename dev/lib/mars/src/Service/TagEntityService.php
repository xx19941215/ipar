<?php
namespace Mars\Service;

class TagEntityService extends \Gap\Service\ServiceBase
{

    public function search($query = [])
    {
        return $this->repo('tag_entity')->search($query);
    }

}