<?php
$this
    ->setCurrentSite('api')

    ->setCurrentAccess('login')
    ->getRest('/feature/product', ['as' => 'ipar-rest-feature-product', 'action' => 'Ipar\Rest\FeatureController@product'])
    ->postRest('/feature/save', ['as' => 'ipar-rest-feature-save-post', 'action' => 'Ipar\Rest\FeatureController@savePost']);

