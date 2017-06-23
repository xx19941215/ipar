<?php
$this->setCurrentSite('www')
    ->setCurrentAccess('public')
    ->get('/brand-tag/{zcode:[A-z0-9]+}', ['as' => 'ipar-ui-brand_tag-show', 'action' => 'Ipar\Ui\BrandTagController@show']);