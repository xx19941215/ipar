<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('login')

    ->getRest('/', ['as' => 'api-index', 'action' => 'IndexController@index'])

    ->postRest('/img/upload-figure', ['as' => 'img-upload-figure', 'action' => 'ImgController@uploadFigurePost'])
    //->postRest('/img/upload-avatar', ['as' => 'img-upload-avatar', 'action' => 'ImgController@uploadAvatarPost'])

    ->postRest('/comment/create', ['as' => 'comment-create', 'action' => 'CommentController@createCommentPost'])
    ->postRest('/comment/delete', ['as' => 'comment-delete', 'action' => 'CommentController@deleteCommentPost'])

    ->postRest('/tag/entity', ['as' => 'tag-entity', 'action' => 'TagController@tagEntityPost'])
    ->postRest('/tag/user', ['as' => 'tag-user', 'action' => 'TagController@tagUserPost'])

    ->postRest('/follow/entity', ['as' => 'follow-entity', 'action' => 'FollowController@followEntityPost'])
    ->postRest('/unfollow/entity', ['as' => 'unfollow-entity', 'action' => 'FollowController@unfollowEntityPost'])
    ->postRest('/follow/user', ['as' => 'follow-user', 'action' => 'FollowController@followUserPost'])
    ->postRest('/unfollow/user', ['as' => 'unfollow-user', 'action' => 'FollowController@unfollowUserPost'])

    ->setCurrentAccess('public')
    ->getRest('/story', ['as' => 'rest-story-index', 'action' => 'Ipar\StoryController@index'])
    ->getRest('/product', ['as' => 'rest-product-index', 'action' => 'Ipar\ProductController@index'])
    ->getRest('/property', ['as' => 'rest-product-index', 'action' => 'Ipar\ProductController@property'])
    ->getRest('/search', ['as' => 'rest-product-index', 'action' => 'Ipar\EntityController@search'])
    ->getRest('/solution', ['as' => 'rest-product-index', 'action' => 'Ipar\RqtController@solution'])

    ->postRest('/comment/later', ['as' => 'comment-later', 'action' => 'CommentController@getLaterCommentsPost'])
    ->postRest('/comment/earlier', ['as' => 'comment-earlier', 'action' => 'CommentController@getEarlierCommentsPost'])
    ->postRest('/comment/latest', ['as' => 'comment-latest', 'action' => 'CommentController@getLatestCommentsPost']);
