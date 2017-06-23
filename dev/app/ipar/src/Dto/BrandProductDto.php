<?php
namespace Ipar\Dto;

class BrandProductDto
{
    public $id;
    public $product_eid;
    public $brand_id;
    public $vote_count;
    public $changed;

    public function getBrandMain()
    {
        return service('brand')->fetchBrandMain($this->brand_id, translator()->getLocaleId());
    }

    public function getBrandTitle()
    {
        $brand_main = $this->getBrandMain();
        return $brand_main->title;
    }

    public function countVote()
    {
        return $this->vote_count;
    }

    public function getVoteSet()
    {
        return service('brand')->schBrandProductVoteSet(['brand_product_id' => $this->id]);
    }

    public function hasVoted()
    {
        return service('brand')->hasVoted($this->id);
    }
}
