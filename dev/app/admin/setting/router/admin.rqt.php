<?php

$this->setCurrentSite('admin')

    ->setCurrentAccess('super')

    // rqt
    ->get('/rqt', ['as' => 'admin-rqt', 'action' => 'Admin\Ui\RqtController@index'])
    ->get('/rqt/search', ['as' => 'admin-rqt-search', 'action' => 'Admin\Ui\RqtController@search'])
    ->get('/rqt/add', ['as' => 'admin-rqt-add', 'action' => 'Admin\Ui\RqtController@add'])
    ->post('/rqt/add', ['as' => 'admin-rqt-add-post', 'action' => 'Admin\Ui\RqtController@addPost'])

    ->get('/rqt/{eid:[0-9]+}', ['as' => 'admin-rqt-show', 'action' => 'Admin\Ui\RqtController@show'])
    ->get('/rqt/{eid:[0-9]+}/content', ['as' => 'admin-rqt-content', 'action' => 'Admin\Ui\RqtController@content'])

    ->get('/rqt/{eid:[0-9]+}/edit', ['as' => 'admin-rqt-edit', 'action' => 'Admin\Ui\RqtController@edit'])
    ->post('/rqt/{eid:[0-9]+}/edit', ['as' => 'admin-rqt-edit-post', 'action' => 'Admin\Ui\RqtController@editPost'])
    ->get('/rqt/{eid:[0-9]+}/delete', ['as' => 'admin-rqt-delete', 'action' => 'Admin\Ui\RqtController@delete'])
    ->post('/rqt/{eid:[0-9]+}/delete', ['as' => 'admin-rqt-delete-post', 'action' => 'Admin\Ui\RqtController@deletePost'])

    ->get('/rqt/{eid:[0-9]+}/solution', ['as' => 'admin-rqt-solution', 'action' => 'Admin\Ui\RqtController@solution'])

    // rqt-idea
    ->get('/rqt/{eid:[0-9]+}/idea', ['as' => 'admin-rqt-idea', 'action' => 'Admin\Ui\RqtController@idea'])
    ->get('/rqt/{eid:[0-9]+}/idea/add', ['as' => 'admin-rqt-idea-add', 'action' => 'Admin\Ui\RqtController@addIdea'])
    ->post('/rqt/{eid:[0-9]+}/idea/add', ['as' => 'admin-rqt-idea-add-post', 'action' => 'Admin\Ui\RqtController@addIdeaPost'])

    // rqt-product
    ->get('/rqt/{eid:[0-9]+}/product', ['as' => 'admin-rqt-product', 'action' => 'Admin\Ui\RqtController@product'])
    ->get('/rqt/{eid:[0-9]+}/product/add', ['as' => 'admin-rqt-product-add', 'action' => 'Admin\Ui\RqtController@addProduct'])
    ->post('/rqt/{eid:[0-9]+}/product/add', ['as' => 'admin-rqt-product-add-post', 'action' => 'Admin\Ui\RqtController@addProductPost']);
