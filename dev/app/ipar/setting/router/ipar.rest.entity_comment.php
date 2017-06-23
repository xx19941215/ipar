<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('login')

    ->postRest('/comment/create', ['as' => 'ipar-rest-entity_comment-create_post', 'action' => 'Ipar\Rest\EntityCommentController@createPost'])
    ->postRest('/comment/delete', ['as' => 'ipar-rest-entity_comment-delete_post', 'action' => 'Ipar\Rest\EntityCommentController@deletePost'])

    ->setCurrentAccess('public')
    //->getRest('/search', ['as' => 'rest-product-index', 'action' => 'Ipar\Rest\EntityController@search'])


    ->postRest('/comment/later', ['as' => 'ipar-rest-entity_comment-later_post', 'action' => 'Ipar\Rest\EntityCommentController@laterPost'])
    ->postRest('/comment/earlier', ['as' => 'ipar-rest-entity_comment-earlier_post', 'action' => 'Ipar\Rest\EntityCommentController@earlierPost'])
    ->postRest('/comment/latest', ['as' => 'ipar-rest-entity_comment-latest_post', 'action' => 'Ipar\Rest\EntityCommentController@latestPost'])
    ->postRest('/comment/conv', ['as' => 'ipar-rest-entity_comment-conv_post', 'action' => 'Ipar\Rest\EntityCommentController@convPost']);

