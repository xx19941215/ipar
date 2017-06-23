<?php
$this
    ->setCurrentSite('www')
    ->setCurrentAccess('public')
    ->get('/appearance', ['as' => 'appearance-index', 'action' => 'Ipar\Ui\AppearanceController@index'])

    ->setCurrentAccess('login')
    ->get('/appearance/create', ['as' => 'appearance-create', 'action' => 'Ipar\Ui\AppearanceController@create'])
    ->post('/appearance/create', ['as' => 'appearance-create-post', 'action' => 'Ipar\Ui\AppearanceController@createPost'])
    ->get(
        '/appearance/{zcode:[0-9a-z-]+}/edit',
        [
            'as' => 'appearance-edit',
            'action' => 'Ipar\Ui\AppearanceController@edit'
        ]
    )
    ->post(
        '/appearance/{zcode:[0-9a-z-]+}/edit',
        [
            'as' => 'appearance-edit-post',
            'action' => 'Ipar\Ui\AppearanceController@editPost'
        ]
    )

    ->get(
        '/appearance/{zcode:[0-9a-z-]+}/delete',
        [
            'as' => 'appearance-delete',
            'action' => 'Ipar\Ui\AppearanceController@delete'
        ]
    )
    ->post(
        '/appearance/{zcode:[0-9a-z-]+}/delete',
        [
            'as' => 'appearance-delete-post',
            'action' => 'Ipar\Ui\AppearanceController@deletePost'
        ]
    )

    ->setCurrentAccess('public')
    ->get('/appearance/{zcode:[0-9a-z-]+}', ['as' => 'appearance-show', 'action' => 'Ipar\Ui\AppearanceController@show'])
    ->get('/appearance/{zcode:[0-9a-z-]+}/comment', ['as' => 'appearance-comment', 'action' => 'Ipar\Ui\AppearanceController@comment']);




