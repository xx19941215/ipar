<?php
$this
    ->setCurrentSite('www')

    ->setCurrentAccess('login')
    ->get('/feature/create', ['as' => 'ipar-feature-create', 'action' => 'Ipar\Ui\FeatureController@create'])
    ->post('/feature/create', ['as' => 'ipar-feature-create-post', 'action' => 'Ipar\Ui\FeatureController@createPost'])
    ->get('/feature/{zcode:[0-9a-z-]+}/edit', ['as' => 'ipar-feature-edit', 'action' => 'Ipar\Ui\FeatureController@edit'])
    ->post('/feature/{zcode:[0-9a-z-]+}/edit', ['as' => 'ipar-feature-edit-post', 'action' => 'Ipar\Ui\FeatureController@editPost'])

    ->setCurrentAccess('public')
    ->get('/feature', ['as' => 'ipar-feature-index', 'action' => 'Ipar\Ui\FeatureController@index'])
    ->get('/feature/tag/{zcode:[0-9a-z-]+}', ['as' => 'ipar-feature-tag', 'action' => 'Ipar\Ui\FeatureController@tag'])
    ->get('/feature/{zcode:[0-9a-z-]+}', ['as' => 'ipar-feature-show', 'action' => 'Ipar\Ui\FeatureController@show'])
    ->get('/feature/{zcode:[0-9a-z-]+}/product', ['as' => 'ipar-feature-product', 'action' => 'Ipar\Ui\FeatureController@product']);

