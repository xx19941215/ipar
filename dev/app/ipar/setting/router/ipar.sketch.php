<?php
$this
    ->setCurrentSite('www')
    ->setCurrentAccess('public')
    ->get('/sketch', ['as' => 'sketch-index', 'action' => 'Ipar\Ui\SketchController@index'])

    ->setCurrentAccess('login')
    ->get('/sketch/create', ['as' => 'sketch-create', 'action' => 'Ipar\Ui\SketchController@create'])
    ->post('/sketch/create', ['as' => 'sketch-create-post', 'action' => 'Ipar\Ui\SketchController@createPost'])
    ->get(
        '/sketch/{zcode:[0-9a-z-]+}/edit',
        [
            'as' => 'sketch-edit',
            'action' => 'Ipar\Ui\SketchController@edit'
        ]
    )
    ->post(
        '/sketch/{zcode:[0-9a-z-]+}/edit',
        [
            'as' => 'sketch-edit-post',
            'action' => 'Ipar\Ui\SketchController@editPost'
        ]
    )

    ->get(
        '/sketch/{zcode:[0-9a-z-]+}/delete',
        [
            'as' => 'sketch-delete',
            'action' => 'Ipar\Ui\SketchController@delete'
        ]
    )
    ->post(
        '/sketch/{zcode:[0-9a-z-]+}/delete',
        [
            'as' => 'sketch-delete-post',
            'action' => 'Ipar\Ui\SketchController@deletePost'
        ]
    )

    ->setCurrentAccess('public')
    ->get('/sketch/{zcode:[0-9a-z-]+}', ['as' => 'sketch-show', 'action' => 'Ipar\Ui\SketchController@show'])
    ->get('/sketch/{zcode:[0-9a-z-]+}/comment', ['as' => 'sketch-comment', 'action' => 'Ipar\Ui\SketchController@comment']);



