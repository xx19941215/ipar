<?php
$this->setCurrentSite('admin')

->setCurrentAccess('admin')



->get('/user', ['as' => 'admin-user', 'action' => 'Admin\Ui\UserController@index'])
->get('/user/search', ['as' => 'admin-user-search', 'action' => 'Admin\Ui\UserController@search'])
->get('/user/{uid:[0-9]+}', ['as' => 'admin-user-show', 'action' => 'Admin\Ui\UserController@show'])

->get('/user/{uid:[0-9]+}/block', ['as' => 'admin-user-block', 'action' => 'Admin\Ui\UserController@block'])
->post('/user/{uid:[0-9]+}/block', ['as' => 'admin-user-block-post', 'action' => 'Admin\Ui\UserController@blockPost'])

->get('/user/{uid:[0-9]+}/activate', ['as' => 'admin-user-activate', 'action' => 'Admin\Ui\UserController@activate'])
->post('/user/{uid:[0-9]+}/activate', ['as' => 'admin-user-activate-post', 'action' => 'Admin\Ui\UserController@activatePost'])

->get('/user/{uid:[0-9]+}/deactivate', ['as' => 'admin-user-deactivate', 'action' => 'Admin\Ui\UserController@deactivate'])
->post('/user/{uid:[0-9]+}/deactivate', ['as' => 'admin-user-deactivate-post', 'action' => 'Admin\Ui\UserController@deactivatePost'])

->get('/user/{uid:[0-9]+}/delete', ['as' => 'admin-user-delete', 'action' => 'Admin\Ui\UserController@delete'])
->post('/user/{uid:[0-9]+}/delete', ['as' => 'admin-user-delete-post', 'action' => 'Admin\Ui\UserController@deletePost'])

->get('/user/{uid:[0-9]+}/assign-privilege', ['as' => 'admin-user-assign-privilege', 'action' => 'Admin\Ui\UserController@assignPrivilege'])
->post('/user/{uid:[0-9]+}/assign-privilege', ['as' => 'admin-user-assign-privilege-post', 'action' => 'Admin\Ui\UserController@assignPrivilegePost'])

->get('/user/{uid:[0-9]+}/assign-role', ['as' => 'admin-user-assign-role', 'action' => 'Admin\Ui\UserController@assignRole'])
->post('/user/{uid:[0-9]+}/assign-role', ['as' => 'admin-user-assign-role-post', 'action' => 'Admin\Ui\UserController@assignRolePost']);
