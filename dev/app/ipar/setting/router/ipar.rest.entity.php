<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('public')

    ->getRest('/search', ['as' => 'ipar-rest-entity-search', 'action' => 'Ipar\Rest\EntityController@search'])
    ->getRest('/search_rqt', ['as' => 'ipar-rest-entity-search_rqt', 'action' => 'Ipar\Rest\EntityController@search'])
    ->getRest('/search_product', ['as' => 'ipar-rest-entity-search_product', 'action' => 'Ipar\Rest\EntityController@search'])
    ->getRest('/suggest', ['as' => 'ipar-rest-entity-suggest', 'action' => 'Ipar\Rest\EntityController@suggest'])
    ->postRest('/fetch', ['as' => 'ipar-rest-entity-fetch-post', 'action' => 'Ipar\Rest\EntityController@fetchPost'])
    ->postRest('/suggest', ['as' => 'ipar-rest-entity-suggest-post', 'action' => 'Ipar\Rest\EntityController@suggestPost']);
