<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('login')

    ->postRest('/like/entity', ['as' => 'ipar-rest-like-entity', 'action' => 'Ipar\Rest\LikeController@likeEntityPost'])
    ->postRest('/unlike/entity', ['as' => 'ipar-rest-unlike-entity', 'action' => 'Ipar\Rest\LikeController@unlikeEntityPost'])
    ->postRest('/like/user', ['as' => 'ipar-rest-like-user', 'action' => 'Ipar\Rest\LikeController@getLikeUser'])
;
