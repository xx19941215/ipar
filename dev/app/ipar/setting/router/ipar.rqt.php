<?php
$this
    ->setCurrentSite('www')

    ->setCurrentAccess('login')
    ->get('/rqt/create', ['as' => 'ipar-rqt-create', 'action' => 'Ipar\Ui\RqtController@create'])
    ->post('/rqt/create', ['as' => 'ipar-rqt-create-post', 'action' => 'Ipar\Ui\RqtController@createPost'])
    ->get('/rqt/{zcode:[0-9a-z-]+}/edit', ['as' => 'ipar-rqt-edit', 'action' => 'Ipar\Ui\RqtController@edit'])
    ->post('/rqt/{zcode:[0-9a-z-]+}/edit', ['as' => 'ipar-rqt-edit-post', 'action' => 'Ipar\Ui\RqtController@editPost'])
    //->get('/rqt/{zcode:[0-9a-z-]+}/delete',['as' => 'ipar-rqt-delete', 'action' => 'Ipar\Ui\RqtController@delete'])
    //->post('/rqt/{zcode:[0-9a-z-]+}/delete', ['as' => 'ipar-rqt-delete-post', 'action' => 'Ipar\Ui\RqtController@deletePost'])

    ->setCurrentAccess('public')
    ->get('/rqt', ['as' => 'ipar-rqt-index', 'action' => 'Ipar\Ui\RqtController@index'])
    ->get('/rqt/tag/{zcode:[0-9a-z-]+}', ['as' => 'ipar-rqt-tag', 'action' => 'Ipar\Ui\RqtController@tag'])
    ->get('/rqt/{zcode:[0-9a-z-]+}', ['as' => 'ipar-rqt-show', 'action' => 'Ipar\Ui\RqtController@show'])
    ->get('/rqt/{zcode:[0-9a-z-]+}/product', ['as' => 'ipar-rqt-product', 'action' => 'Ipar\Ui\RqtController@product'])
    ->get('/rqt/{zcode:[0-9a-z-]+}/idea', ['as' => 'ipar-rqt-idea', 'action' => 'Ipar\Ui\RqtController@idea']);

    //->get('/rqt/{zcode:[0-9a-z-]+}/idea', ['as' => 'ipar-rqt-idea', 'action' => 'Ipar\Ui\RqtController@idea']);
    //->get('/rqt/{zcode:[0-9a-z-]+}/invent', ['as' => 'ipar-rqt-invent', 'action' => 'Ipar\Ui\RqtController@invent'])
    //->get('/rqt/{zcode:[0-9a-z-]+}/spu', ['as' => 'rqt-spu', 'action' => 'Ipar\Ui\RqtController@spu'])
    //->get('/rqt/{zcode:[0-9a-z-]+}/comment', ['as' => 'rqt-comment', 'action' => 'Ipar\Ui\RqtController@comment']);

