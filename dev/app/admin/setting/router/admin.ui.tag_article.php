<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')
    ->get('/tag/{tag_id:[0-9]+}/article', ['as' => 'admin-ui-tag-show_article', 'action' => 'Admin\Ui\TagArticleController@showArticle'])
    ->get('/article/{zcode:[0-9a-z-]+}/tag/add_multiple', ['as' => 'admin-ui-tag_article-add_multiple', 'action' => 'Admin\Ui\TagArticleController@addTagMultiple'])
    ->post('/article/{zcode:[0-9a-z-]+}/tag/add_multiple', ['as' => 'admin-ui-tag_article-add_multiple-post', 'action' => 'Admin\Ui\TagArticleController@addTagMultiplePost']);