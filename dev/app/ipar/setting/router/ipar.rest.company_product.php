<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('public')
    ->getRest('/company/product', ['as' => 'ipar-rest-company_product-show', 'action' => 'Ipar\Rest\CompanyProductController@product'])
;
