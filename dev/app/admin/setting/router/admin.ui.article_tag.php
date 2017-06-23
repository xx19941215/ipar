<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')
    ->get('/article/{zcode:[0-9a-z-]+}/tag', ['as' => 'admin-ui-article_tag-search', 'action' => 'Admin\Ui\ArticleTagController@search'])
    ->get('/article/{zcode:[0-9a-z-]+}/tag/add', ['as' => 'admin-ui-article_tag-add', 'action' => 'Admin\Ui\ArticleTagController@add'])
    ->post('/article/{zcode:[0-9a-z-]+}/tag/add', ['as' => 'admin-ui-article_tag-add-post', 'action' => 'Admin\Ui\ArticleTagController@addPost'])
    ->get('/article/{zcode:[0-9a-z]+}/tag/{tag_id:[0-9]+}/unlink', ['as' => 'admin-ui-article_tag-unlink', 'action' => 'Admin\Ui\ArticleTagController@unlink'])
    ->post('/article/{zcode:[0-9a-z]+}/tag/{tag_id:[0-9]+}/unlink', ['as' => 'admin-ui-article_tag-unlink-post', 'action' => 'Admin\Ui\ArticleTagController@unlinkPost']);


