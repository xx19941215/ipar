<?php
$this->setCurrentSite('admin')
    ->setCurrentAccess('super')
    ->get('/tag', ['as' => 'admin-ui-tag-index', 'action' => 'Admin\Ui\TagController@index'])
    ->get('/tag/{tag_id:[0-9]+}', ['as' => 'admin-ui-tag-show', 'action' => 'Admin\Ui\TagController@show'])
    ->get('/tag/add', ['as' => 'admin-ui-tag-add', 'action' => 'Admin\Ui\TagController@add'])
    ->post('/tag/add', ['as' => 'admin-ui-tag-add-post', 'action' => 'Admin\Ui\TagController@addPost'])
    ->get('/tag/{tag_id:[0-9]+}/delete', ['as' => 'amdin-ui-tag-delete', 'action' => 'Admin\Ui\TagController@delete'])
    ->post('/tag/{tag_id:[0-9]+}/delete', ['as' => 'amdin-ui-tag-delete-post', 'action' => 'Admin\Ui\TagController@deletePost'])
    ->get('/tag/{tag_id:[0-9]+}/edit', ['as' => 'admin-ui-tag-edit', 'action' => 'Admin\Ui\TagController@edit'])
    ->post('/tag/{tag_id:[0-9]+}/edit', ['as' => 'admin-ui-tag-edit-post', 'action' => 'Admin\Ui\TagController@editPost'])
    ->get('/tag/{tag_id:[0-9]+}/activate', ['as' => 'admin-ui-tag-activate', 'action' => 'Admin\Ui\TagController@activate'])
    ->post('/tag/{tag_id:[0-9]+}/activate', ['as' => 'admin-ui-tag-activate-post', 'action' => 'Admin\Ui\TagController@activatePost'])
    ->get('/tag/{tag_id:[0-9]+}/deactivate', ['as' => 'admin-ui-tag-deactivate', 'action' => 'Admin\Ui\TagController@deactivate'])
    ->post('/tag/{tag_id:[0-9]+}/deactivate', ['as' => 'admin-ui-tag-deactivate-post', 'action' => 'Admin\Ui\TagController@deactivatePost'])
    ->get('/tag/{tag_id:[0-9]+}/logo', ['as' => 'admin-ui-tag-logo-upload', 'action' => 'Admin\Ui\TagController@logoUpload'])
    ->post('/tag/{tag_id:[0-9]+}/logo', ['as' => 'admin-ui-tag-logo-uploadPost', 'action' => 'Admin\Ui\TagController@logoUploadPost']);



