<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')

    ->get('/friend-link/add', ['as' => 'admin-ui-friend-link-add', 'action' => 'Admin\Ui\FriendLinkController@add'])
    ->post('/friend-link/add', ['as' => 'admin-ui-friend-link-add', 'action' => 'Admin\Ui\FriendLinkController@addPost'])
    ->get('/friend-link/{friend_link_id:[0-9]+}/edit', ['as' => 'admin-ui-friend-link-edit', 'action' => 'Admin\Ui\FriendLinkController@edit'])
    ->post('/friend-link/{friend_link_id:[0-9]+}/edit', ['as' => 'admin-ui-friend-link-edit-post', 'action' => 'Admin\Ui\FriendLinkController@editPost'])
    ->get('/friend-link/{friend_link_id:[0-9]+}/deactivate', ['as' => 'admin-ui-friend-link-deactivate', 'action' => 'Admin\Ui\FriendLinkController@deactivate'])
    ->post('/friend-link/{friend_link_id:[0-9]+}/deactivate', ['as' => 'admin-ui-friend-link-deactivate-post', 'action' => 'Admin\Ui\FriendLinkController@deactivatePost'])
    ->get('/friend-link/{friend_link_id:[0-9]+}/activate', ['as' => 'admin-ui-friend-link-activate', 'action' => 'Admin\Ui\FriendLinkController@activate'])
    ->post('/friend-link/{friend_link_id:[0-9]+}/activate', ['as' => 'admin-ui-friend-link-activate-post', 'action' => 'Admin\Ui\FriendLinkController@activatePost'])
    ->get('/friend-link', ['as' => 'admin-ui-friend-link-index', 'action' => 'Admin\Ui\FriendLinkController@index']);
