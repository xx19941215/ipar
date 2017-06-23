<?php

$this->setCurrentSite('admin')

    ->setCurrentAccess('super')

    // feature
    ->get('/feature', ['as' => 'admin-feature', 'action' => 'Admin\Ui\FeatureController@index'])
    ->get('/feature/search', ['as' => 'admin-feature-search', 'action' => 'Admin\Ui\FeatureController@search'])
    ->get('/feature/add', ['as' => 'admin-feature-add', 'action' => 'Admin\Ui\FeatureController@add'])
    ->post('/feature/add', ['as' => 'admin-feature-add-post', 'action' => 'Admin\Ui\FeatureController@addPost'])

    ->get('/feature/{eid:[0-9]+}', ['as' => 'admin-feature-show', 'action' => 'Admin\Ui\FeatureController@show'])
    ->get('/feature/{eid:[0-9]+}/content', ['as' => 'admin-feature-content', 'action' => 'Admin\Ui\FeatureController@content'])

    ->get('/feature/{eid:[0-9]+}/edit', ['as' => 'admin-feature-edit', 'action' => 'Admin\Ui\FeatureController@edit'])
    ->post('/feature/{eid:[0-9]+}/edit', ['as' => 'admin-feature-edit-post', 'action' => 'Admin\Ui\FeatureController@editPost'])
    ->get('/feature/{eid:[0-9]+}/delete', ['as' => 'admin-feature-delete', 'action' => 'Admin\Ui\FeatureController@delete'])
    ->post('/feature/{eid:[0-9]+}/delete', ['as' => 'admin-feature-delete-post', 'action' => 'Admin\Ui\FeatureController@deletePost'])

    ->get('/feature/{eid:[0-9]+}/solution', ['as' => 'admin-feature-solution', 'action' => 'Admin\Ui\FeatureController@solution'])

    // feature-product
    ->get('/feature/{eid:[0-9]+}/product', ['as' => 'admin-feature-product', 'action' => 'Admin\Ui\FeatureController@product'])
    ->get('/feature/{eid:[0-9]+}/product/add', ['as' => 'admin-feature-product-add', 'action' => 'Admin\Ui\FeatureController@addProduct'])
    ->post('/feature/{eid:[0-9]+}/product/add', ['as' => 'admin-feature-product-add-post', 'action' => 'Admin\Ui\FeatureController@addProductPost']);
