<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')
    ->get('/product/{eid:[0-9]+}/comment', ['as' => 'admin-ui-product_comment-index', 'action' => 'Admin\Ui\ProductCommentController@index'])
    ->get('/product/{eid:[0-9]+}/comment/{comment_id:[0-9]+}/deactivate', ['as' => 'admin-ui-product_comment-deactivate', 'action' => 'Admin\Ui\ProductCommentController@deactivate'])
    ->post('/product/{eid:[0-9]+}/comment/{comment_id:[0-9]+}/deactivate', ['as' => 'admin-ui-product_comment-deactivate-post', 'action' => 'Admin\Ui\ProductCommentController@deactivatePost'])
    ->get('/product/{eid:[0-9]+}/comment/{comment_id:[0-9]+}/activate', ['as' => 'admin-ui-product_comment-activate', 'action' => 'Admin\Ui\ProductCommentController@activate'])
    ->post('/product/{eid:[0-9]+}/comment/{comment_id:[0-9]+}/activate', ['as' => 'admin-ui-product_comment-activate-post', 'action' => 'Admin\Ui\ProductCommentController@activatePost']);