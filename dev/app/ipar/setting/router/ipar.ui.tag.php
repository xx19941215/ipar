<?php
$this->setCurrentSite('www')
    ->setCurrentAccess('public')
    ->get('/tag/{zcode:[0-9a-z-]+}', ['as' => 'ipar-ui-tag-index', 'action' => 'Ipar\Ui\TagController@index'])
    ->get('/tag/{zcode:[0-9a-z-]+}/rqt', ['as' => 'ipar-ui-tag_rqt-index', 'action' => 'Ipar\Ui\TagRqtController@index'])
    ->get('/tag/{zcode:[0-9a-z-]+}/product', ['as' => 'ipar-ui-tag_product-index', 'action' => 'Ipar\Ui\TagProductController@index'])
    ->get('/tag/{zcode:[0-9a-z-]+}/article', ['as' => 'ipar-ui-tag_article-index', 'action' => 'Ipar\Ui\TagArticleController@index']);
