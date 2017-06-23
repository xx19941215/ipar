<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('login')

    ->postRest('/comment_article/create', ['as' => 'ipar-rest-comment_article-create', 'action' => 'Ipar\Rest\ArticleCommentController@createPost'])
    ->postRest('/comment_article/delete', ['as' => 'ipar-rest-comment_article-delete', 'action' => 'Ipar\Rest\ArticleCommentController@deletePost'])

    ->setCurrentAccess('public')
    //->getRest('/search', ['as' => 'rest-product-index', 'action' => 'Ipar\Rest\EntityController@search'])

    ->postRest('/comment_article/later', ['as' => 'ipar-rest-comment_article-later_post', 'action' => 'Ipar\Rest\ArticleCommentController@laterPost'])
    ->postRest('/comment_article/earlier', ['as' => 'ipar-rest-comment_article-earlier_post', 'action' => 'Ipar\Rest\ArticleCommentController@earlierPost'])
    ->postRest('/comment_article/latest', ['as' => 'ipar-rest-comment_article-latest_post', 'action' => 'Ipar\Rest\ArticleCommentController@latestPost'])
    ->postRest('/comment_article/conv', ['as' => 'ipar-rest-comment_article-conv_post', 'action' => 'Ipar\Rest\ArticleCommentController@convPost']);

