<?php
$this
    ->setCurrentSite('www')

    ->setCurrentAccess('login')
    ->get('/product/create', ['as' => 'ipar-product-create', 'action' => 'Ipar\Ui\ProductController@create'])
    ->post('/product/create', ['as' => 'ipar-product-create-post', 'action' => 'Ipar\Ui\ProductController@createPost'])
    ->get('/product/{zcode:[0-9a-z-]+}/edit', ['as' => 'ipar-product-edit', 'action' => 'Ipar\Ui\ProductController@edit'])
    ->post('/product/{zcode:[0-9a-z-]+}/edit', ['as' => 'ipar-product-edit-post', 'action' => 'Ipar\Ui\ProductController@editPost'])

    ->setCurrentAccess('public')
    ->get('/product', ['as' => 'ipar-product-index', 'action' => 'Ipar\Ui\ProductController@index'])
    ->get('/product/all', ['as' => 'ipar-product-all', 'action' => 'Ipar\Ui\ProductController@all'])
    ->get('/product/tag/{zcode:[0-9a-z-]+}', ['as' => 'ipar-product-tag', 'action' => 'Ipar\Ui\ProductController@tag'])
    ->get('/product/brand/{zcode:[0-9a-z-]+}', ['as' => 'ipar-product-brand', 'action' => 'Ipar\Ui\ProductController@brand'])
    ->get('/product/{zcode:[0-9a-z-]+}', ['as' => 'ipar-product-show', 'action' => 'Ipar\Ui\ProductController@show'])
    ->get('/product/{zcode:[0-9a-z-]+}/feature', ['as' => 'ipar-product-feature', 'action' => 'Ipar\Ui\ProductController@feature'])
    ->get('/product/{zcode:[0-9a-z-]+}/solved', ['as' => 'ipar-product-solved', 'action' => 'Ipar\Ui\ProductController@solved'])
    ->get('/product/{zcode:[0-9a-z-]+}/improving', ['as' => 'ipar-product-improving', 'action' => 'Ipar\Ui\ProductController@improving']);

