<?php
$this->setCurrentSite('admin')
    ->setCurrentAccess('admin')

    ->get('/role', ['as' => 'admin-role', 'action' => 'Admin\Ui\RoleController@index'])
    ->get('/role/search', ['as' => 'admin-role-search', 'action' => 'Admin\Ui\RoleController@search'])
    ->get('/role/add', ['as' => 'admin-role-add', 'action' => 'Admin\Ui\RoleController@add'])
    ->post('/role/add', ['as' => 'admin-role-add-post', 'action' => 'Admin\Ui\RoleController@addPost'])
    ->get('/role/{role_id:[0-9]+}', ['as' => 'admin-role-show', 'action' => 'Admin\Ui\RoleController@show'])
    ->get('/role/{role_id:[0-9]+}/edit', ['as' => 'admin-role-edit', 'action' => 'Admin\Ui\RoleController@edit'])
    ->post('/role/{role_id:[0-9]+}/edit', ['as' => 'admin-role-edit-post', 'action' => 'Admin\Ui\RoleController@editPost'])
    ->get('/role/{role_id:[0-9]+}/activate', ['as' => 'admin-role-activate', 'action' => 'Admin\Ui\RoleController@activate'])
    ->post('/role/{role_id:[0-9]+}/activate', ['as' => 'admin-role-activate-post', 'action' => 'Admin\Ui\RoleController@activatePost'])
    ->get('/role/{role_id:[0-9]+}/delete', ['as' => 'admin-role-delete', 'action' => 'Admin\Ui\RoleController@delete'])
    ->post('/role/{role_id:[0-9]+}/delete', ['as' => 'admin-role-delete-post', 'action' => 'Admin\Ui\RoleController@deletePost'])
    ->get('/role/{role_id:[0-9]+}/deactivate', ['as' => 'admin-role-deactivate', 'action' => 'Admin\Ui\RoleController@deactivate'])
    ->post('/role/{role_id:[0-9]+}/deactivate', ['as' => 'admin-role-deactivate-post', 'action' => 'Admin\Ui\RoleController@deactivatePost']);
