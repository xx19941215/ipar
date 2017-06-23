<?php
namespace Ipar\Dto;

class FeatureDto extends \Mars\Dto\EntityDto {

    public function countFproduct()
    {
        return service('feature')->countFproduct($this->eid);
    }

}
