<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('login')
    //->getRest('/', ['as' => 'api-index', 'action' => 'Ipar\Rest\IndexController@index']);
    ->postRest('/user/upload-avt', ['as' => 'api-user-upload-avt', 'action' => 'Ipar\Rest\UserController@uploadAvtPost'])


    ->setCurrentAccess('public')
    ->getRest('/search_user', ['as' => 'ipar-rest-user-search_user', 'action' => 'Ipar\Rest\UserController@search']);
