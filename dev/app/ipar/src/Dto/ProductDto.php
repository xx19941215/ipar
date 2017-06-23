<?php
namespace Ipar\Dto;

class ProductDto extends \Mars\Dto\EntityDto
{
    public $title;
    public $content;
    public $release_date;
    public $url;

    public function countProperty()
    {
        return service('product')->countProperty($this->eid);
    }

    public function countPsolved()
    {
        return service('product')->countPsolved($this->eid);
    }

    public function countPimproving()
    {
        return service('product')->countPimproving($this->eid);
    }

    public function countPfeature()
    {
        return service('product')->countPfeature($this->eid);
    }

    public function countPbranch()
    {
        return service('product')->countPbranch($this->eid);
    }

    public function getBrandProductSet()
    {
        return service('brand')->schBrandProductSet(['product_eid' => $this->eid])->setCountPerPage(3);
    }
}
