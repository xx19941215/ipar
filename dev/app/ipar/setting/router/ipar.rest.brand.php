<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('login')

    ->postRest('/brand/save', ['as' => 'ipar-rest-brand-save', 'action' => 'Ipar\Rest\BrandController@savePost'])
    ->postRest('/brand/delete', ['as' => 'ipar-rest-brand-delete', 'action' => 'Ipar\Rest\BrandController@deletePost'])
    ->postRest('/brand/vote', ['as' => 'ipar-rest-brand-vote', 'action' => 'Ipar\Rest\BrandController@votePost'])
    ->postRest('/brand/unvote', ['as' => 'ipar-rest-brand-unvote', 'action' => 'Ipar\Rest\BrandController@unvotePost'])
    ->postRest('/brand/list', ['as' => 'ipar-rest-brand-list', 'action' => 'Ipar\Rest\BrandController@listPost'])
    ->postRest('/brand/vote-users', ['as' => 'ipar-rest-brand-vote-users', 'action' => 'Ipar\Rest\BrandController@voteUsersPost']);
