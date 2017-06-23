<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')
    ->get('/article/{zcode:[0-9a-z-]+}/comment', ['as' => 'admin-ui-article_comment-index', 'action' => 'Admin\Ui\ArticleCommentController@index'])
    ->get('/article/{zcode:[0-9a-z-]+}/comment/{comment_id:[0-9]+}/deactivate', ['as' => 'admin-ui-article_comment-deactivate', 'action' => 'Admin\Ui\ArticleCommentController@deactivate'])
    ->post('/article/{zcode:[0-9a-z-]+}/comment/{comment_id:[0-9]+}/deactivate', ['as' => 'admin-ui-article_comment-deactivate-post', 'action' => 'Admin\Ui\ArticleCommentController@deactivatePost'])
    ->get('/article/{zcode:[0-9a-z-]+}/comment/{comment_id:[0-9]+}/activate', ['as' => 'admin-ui-article_comment-activate', 'action' => 'Admin\Ui\ArticleCommentController@activate'])
    ->post('/article/{zcode:[0-9a-z-]+}/comment/{comment_id:[0-9]+}/activate', ['as' => 'admin-ui-article_comment-activate-post', 'action' => 'Admin\Ui\ArticleCommentController@activatePost']);