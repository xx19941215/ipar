<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('public')
    ->getRest('/brand-tag/product', ['as' => 'ipar-rest-brand_tag_product-show', 'action' => 'Ipar\Rest\BrandTagProductController@show']);
