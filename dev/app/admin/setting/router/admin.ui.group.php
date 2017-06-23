<?php
$this->setCurrentSite('admin')
    ->setCurrentAccess('super')
    ->get('/group', ['as' => 'admin-ui-group-index', 'action' => 'Admin\Ui\GroupController@index'])
    ->get('/group/{gid:[0-9]+}', ['as' => 'admin-ui-group-show', 'action' => 'Admin\Ui\GroupController@show'])
    ->get('/group/{gid:[0-9]+}/edit', ['as' => 'admin-ui-group-edit', 'action' => 'Admin\Ui\GroupController@edit'])
    ->post('/group/{gid:[0-9]+}/edit', ['as' => 'admin-ui-group-edit-post',
        'action' => 'Admin\Ui\GroupController@editPost'])
    ->get('/group/add', ['as' => 'admin-ui-group-add', 'action' => 'Admin\Ui\GroupController@add'])
    ->post('/group/add', ['as' => 'admin-ui-group-add-post', 'action' => 'Admin\Ui\GroupController@addPost'])
    ->get('/group/{gid:[0-9]+}/deactivate', ['as' => 'admin-ui-group-deactivate', 'action' =>
        'Admin\Ui\GroupController@deactivate'])
    ->post('/group/{gid:[0-9]+}/deactivate', ['as' => 'admin-ui-group-deactivate-post', 'action' =>
        'Admin\Ui\GroupController@deactivatePost'])
    ->get('/group/{gid:[0-9]+}/activate', ['as' => 'admin-ui-group-activate', 'action' =>
        'Admin\Ui\GroupController@activate'])
    ->post('/group/{gid:[0-9]+}/activate', ['as' => 'admin-ui-group-activate-post', 'action' =>
        'Admin\Ui\GroupController@activatePost'])
    ->get('/group/{gid:[0-9]+}/delete', ['as' => 'admin-ui-group-delete', 'action' =>
        'Admin\Ui\GroupController@delete'])
    ->post('/group/{gid:[0-9]+}/delete', ['as' => 'admin-ui-group-delete-post', 'action' =>
        'Admin\Ui\GroupController@deletePost'])
    ->get('/group/{gid:[0-9]+}/logo', ['as' => 'admin-ui-group-logo', 'action' =>
        'Admin\Ui\GroupLogoController@edit'])
    ->post('/group/{gid:[0-9]+}/logo', ['as' => 'admin-ui-group-logo_post', 'action' =>
        'Admin\Ui\GroupLogoController@editPost'])
    ->get('/group/{gid:[0-9]+}/contact', ['as' => 'admin-ui-group_contact-list', 'action' =>
        'Admin\Ui\GroupContactController@list'])
    ->get('/group/{gid:[0-9]+}/contact/add', ['as' => 'admin-ui-group_contact-add', 'action' =>
        'Admin\Ui\GroupContactController@add'])
    ->post('/group/{gid:[0-9]+}/contact/add', ['as' => 'admin-ui-group_contact-add-post',
        'action' => 'Admin\Ui\GroupContactController@addPost'])
    ->get('/group/{gid:[0-9]+}/contact/{group_contact_id:[0-9]+}/edit', ['as' => 'admin-ui-group_contact-edit',
        'action' => 'Admin\Ui\GroupContactController@edit'])
    ->post('/group/{gid:[0-9]+}/contact/{group_contact_id:[0-9]+}/edit',
        ['as' => 'admin-ui-group_contact-edit-post', 'action' => 'Admin\Ui\GroupContactController@editPost'])
    ->get('/group/{gid:[0-9]+}/contact/{group_contact_id:[0-9]+}/delete', ['as' => 'admin-ui-group_contact-delete',
        'action' => 'Admin\Ui\GroupContactController@delete'])
    ->post('/group/{gid:[0-9]+}/contact/{group_contact_id:[0-9]+}/delete',
        ['as' => 'admin-ui-group_contact-delete-post', 'action' => 'Admin\Ui\GroupContactController@deletePost'])
    ->get('/group/{gid:[0-9]+}/office', ['as' => 'admin-ui-group_office-list', 'action' =>
        'Admin\Ui\GroupOfficeController@list'])
    ->get('/group/{gid:[0-9]+}/office/add', ['as' => 'admin-ui-group_office-add', 'action' =>
        'Admin\Ui\GroupOfficeController@add'])
    ->post('/group/{gid:[0-9]+}/office/add', ['as' => 'admin-ui-group_office-add-post', 'action' =>
        'Admin\Ui\GroupOfficeController@addPost'])
    ->get('/group/{gid:[0-9]+}/office/{group_office_id:[0-9]+}/edit', ['as' => 'admin-ui-group_office-edit',
        'action' => 'Admin\Ui\GroupOfficeController@edit'])
    ->post('/group/{gid:[0-9]+}/office/{group_office_id:[0-9]+}/edit',
        ['as' => 'admin-ui-group_office-edit-post', 'action' => 'Admin\Ui\GroupOfficeController@editPost'])
    ->get('/group/{gid:[0-9]+}/office/{group_office_id:[0-9]+}/delete', ['as' => 'admin-ui-group_office-delete',
        'action' => 'Admin\Ui\GroupOfficeController@delete'])
    ->post('/group/{gid:[0-9]+}/office/{group_office_id:[0-9]+}/delete',
        ['as' => 'admin-ui-group_office-delete-post', 'action' => 'Admin\Ui\GroupOfficeController@deletePost'])
    ->get('/group/{gid:[0-9]+}/social', ['as' => 'admin-ui-group_social-list', 'action' =>
        'Admin\Ui\GroupSocialController@list'])
    ->get('/group/{gid:[0-9]+}/social/add', ['as' => 'admin-ui-group_social-add', 'action' =>
        'Admin\Ui\GroupSocialController@add'])
    ->post('/group/{gid:[0-9]+}/social/add', ['as' => 'admin-ui-group_social-add-post', 'action' =>
        'Admin\Ui\GroupSocialController@addPost'])
    ->get('/group/{gid:[0-9]+}/social/{group_social_id:[0-9]+}/edit', ['as' => 'admin-ui-group_social-edit',
        'action' => 'Admin\Ui\GroupSocialController@edit'])
    ->post('/group/{gid:[0-9]+}/social/{group_social_id:[0-9]+}/edit', ['as' => 'admin-ui-group_social-edit-post',
        'action' => 'Admin\Ui\GroupSocialController@editPost'])
    ->get('/group/{gid:[0-9]+}/social/{group_social_id:[0-9]+}/delete', ['as' => 'admin-ui-group_social-delete',
        'action' => 'Admin\Ui\GroupSocialController@delete'])
    ->post('/group/{gid:[0-9]+}/social/{group_social_id:[0-9]+}/delete',
        ['as' => 'admin-ui-group_social-delete-post', 'action' => 'Admin\Ui\GroupSocialController@deletePost']);