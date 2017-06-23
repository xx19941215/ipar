<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('login')

    ->postRest('/follow/entity', ['as' => 'api-follow-entity', 'action' => 'Ipar\Rest\FollowController@followEntityPost'])
    ->postRest('/unfollow/entity', ['as' => 'api-unfollow-entity', 'action' => 'Ipar\Rest\FollowController@unfollowEntityPost'])
    ->postRest('/follow/user', ['as' => 'api-follow-user', 'action' => 'Ipar\Rest\FollowController@followUserPost'])
    ->postRest('/unfollow/user', ['as' => 'api-unfollow-user', 'action' => 'Ipar\Rest\FollowController@unfollowUserPost'])
    ->postRest('/follow/group', ['as' => 'api-follow-group', 'action' => 'Ipar\Rest\GroupFollowController@followGroupPost'])
    ->postRest('/unfollow/group', ['as' => 'api-unfollow-group', 'action' => 'Ipar\Rest\GroupFollowController@unfollowGroupPost'])

    ->postRest('/user/common-users', ['as' => 'api-user-common-users', 'action' => 'Ipar\Rest\FollowController@userCommonUsers'])


    ->setCurrentAccess('public')

    ->postRest('/user/followed-users', ['as' => 'api-user-followed-users', 'action' => 'Ipar\Rest\FollowController@userFollowedUsers'])
    ->postRest('/user/following-users', ['as' => 'api-user-following-users', 'action' => 'Ipar\Rest\FollowController@userFollowingUsers'])
    ->postRest('/entity/followed-users', ['as' => 'api-entity-followed-users', 'action' => 'Ipar\Rest\FollowController@entityFollowedUsers']);
