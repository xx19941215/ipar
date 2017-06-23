<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')
    ->get('/product/{eid:[0-9]+}/tag', ['as' => 'admin-ui-product_tag-search', 'action' => 'Admin\Ui\ProductTagController@search'])
    ->get('/product/{eid:[0-9]+}/tag/add', ['as' => 'admin-ui-product_tag-add', 'action' => 'Admin\Ui\ProductTagController@addTag'])
    ->post('/product/{eid:[0-9]+}/tag/add', ['as' => 'admin-ui-product_tag-add-post', 'action' => 'Admin\Ui\ProductTagController@addTagPost'])
    ->get('/product/{eid:[0-9]+}/tag/{tag_id:[0-9]+}/unlink', ['as' => 'admin-ui-product_tag-unlink', 'action' => 'Admin\Ui\ProductTagController@unlink'])
    ->post('/product/{eid:[0-9]+}/tag/{tag_id:[0-9]+}/unlink', ['as' => 'admin-ui-product_tag-unlink-post', 'action' => 'Admin\Ui\ProductTagController@unlinkPost']);
