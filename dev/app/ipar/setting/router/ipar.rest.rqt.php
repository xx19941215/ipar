<?php
$this
    ->setCurrentSite('api')

    ->setCurrentAccess('login')
    ->postRest('/rqt/save', ['as' => 'ipar-rest-rqt-save-post', 'action' => 'Ipar\Rest\RqtController@savePost'])
    ->postRest('/rqt/save_idea', ['as' => 'ipar-rest-rqt-save-idea-post', 'action' => 'Ipar\Rest\RqtController@saveIdeaPost'])
    ->postRest('/rqt/save_product', ['as' => 'ipar-rest-rqt-save-product-post', 'action' => 'Ipar\Rest\RqtController@saveProductPost'])
    ->postRest('/rqt/save_solution', ['as' => 'ipar-rest-rqt-save-solution-post', 'action' => 'Ipar\Rest\RqtController@saveSolutionPost'])

    ->setCurrentAccess('public')
    //->getRest('/search', ['as' => 'rest-product-index', 'action' => 'Api\Rest\EntityController@search'])
    ->getRest('/solution', ['as' => 'ipar-rest-rqt-solution', 'action' => 'Ipar\Rest\RqtController@solution'])
    ->getRest('/rqt', ['as' => 'ipar-rest-rqt-index', 'action' => 'Ipar\Rest\RqtController@index']);
