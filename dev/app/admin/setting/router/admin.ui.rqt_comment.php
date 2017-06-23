<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')
    ->get('/rqt/{eid:[0-9]+}/comment', ['as' => 'admin-ui-rqt_comment-index', 'action' => 'Admin\Ui\RqtCommentController@index'])
    ->get('/rqt/{eid:[0-9]+}/comment/{comment_id:[0-9]+}/deactivate', ['as' => 'admin-ui-rqt_comment-deactivate', 'action' => 'Admin\Ui\RqtCommentController@deactivate'])
    ->post('/rqt/{eid:[0-9]+}/comment/{comment_id:[0-9]+}/deactivate', ['as' => 'admin-ui-rqt_comment-deactivate-post', 'action' => 'Admin\Ui\RqtCommentController@deactivatePost'])
    ->get('/rqt/{eid:[0-9]+}/comment/{comment_id:[0-9]+}/activate', ['as' => 'admin-ui-rqt_comment-activate', 'action' => 'Admin\Ui\RqtCommentController@activate'])
    ->post('/rqt/{eid:[0-9]+}/comment/{comment_id:[0-9]+}/activate', ['as' => 'admin-ui-rqt_comment-activate-post', 'action' => 'Admin\Ui\RqtCommentController@activatePost']);
