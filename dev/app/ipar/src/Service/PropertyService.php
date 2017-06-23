<?php

namespace Ipar\Service;

class PropertyService extends \Mars\Service\EntityService
{
    protected $property_repo;

    public function bootstrap()
    {
        $this->property_repo = $this->repo('property_repo');
        parent::bootstrap();
    }

    public function isVoted($property_id,$uid){
        return $this->property_repo->ifVoted($property_id,$uid);
    }
}
