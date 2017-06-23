<?php
$this
    ->setCurrentSite('wx')
    ->setCurrentAccess('public')

    ->get('/oauth/callback', ['as' => 'wx-oauth-callback', 'action' => 'Sns\Wx\AuthController@oauthCallback'])

    ->get('/new/user', ['as' => 'wx-new-user',          'action' => 'Sns\Wx\AuthController@newUser'])
    ->post('/new/user', ['as' => 'wx-new-user-post',    'action' => 'Sns\Wx\AuthController@newUserPost'])

    ->get('/bind/user', ['as' => 'wx-bind-user',        'action' => 'Sns\Wx\AuthController@bindUser'])
    ->post('/bind/user', ['as' => 'wx-bind-user-post',  'action' => 'Sns\Wx\AuthController@bindUserPost']);
