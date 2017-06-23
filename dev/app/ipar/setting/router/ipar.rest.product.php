<?php
$this
    ->setCurrentSite('api')

    ->setCurrentAccess('login')
    ->postRest('/product/save', ['as' => 'ipar-rest-product-save-post', 'action' => 'Ipar\Rest\ProductController@savePost'])
    ->postRest('/product/save_feature', ['as' => 'ipar-rest-product-save-feature-post', 'action' => 'Ipar\Rest\ProductController@saveFeaturePost'])
    ->postRest('/product/save_solved', ['as' => 'ipar-rest-product-save-solved-post', 'action' => 'Ipar\Rest\ProductController@saveSolvedPost'])
    ->postRest('/product/save_improving', ['as' => 'ipar-rest-product-save-improving-post', 'action' => 'Ipar\Rest\ProductController@saveImprovingPost'])
    //->postRest('/product/save_rqt', ['as' => 'ipar-rest-product-save-rqt-post', 'action' => 'Ipar\Rest\ProductController@saveRqtPost'])
    ->postRest('/product/save_property', ['as' => 'ipar-rest-product-save-property-post', 'action' => 'Ipar\Rest\ProductController@savePropertyPost'])

    ->setCurrentAccess('public')
    ->getRest('/product', ['as' => 'ipar-rest-product-index', 'action' => 'Ipar\Rest\ProductController@index'])
    ->getRest('/property', ['as' => 'ipar-rest-product-property', 'action' => 'Ipar\Rest\ProductController@property']);
