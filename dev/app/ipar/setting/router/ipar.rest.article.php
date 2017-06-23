<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('public')

    ->getRest('/search_article', ['as' => 'ipar-rest-search_article', 'action' => 'Ipar\Rest\ArticleController@search'])
    ->getRest('/article/suggest', ['as' => 'ipar-rest-article-suggest', 'action' => 'Ipar\Rest\ArticleController@suggest'])
    ->postRest('/article/fetch', ['as' => 'ipar-rest-article-fetch-post', 'action' => 'Ipar\Rest\ArticleController@fetchPost'])
    ->postRest('/article/suggest', ['as' => 'ipar-rest-article-suggest-post', 'action' => 'Ipar\Rest\ArticleController@suggestPost'])
    ->getRest('/article', ['as' => 'ipar-rest-article-index', 'action' => 'Ipar\Rest\ArticleController@index'])
;
