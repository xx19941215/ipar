<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('public')
    ->getRest('/tag_rqt', ['as' => 'ipar-rest-tag_rqt-index', 'action' => 'Ipar\Rest\TagRqtController@index'])
    ->getRest('/tag_product', ['as' => 'ipar-rest-tag_product-index', 'action' => 'Ipar\Rest\TagProductController@index'])
    ->getRest('/tag_article', ['as' => 'ipar-rest-tag_article-index', 'action' => 'Ipar\Rest\TagArticleController@index']);
