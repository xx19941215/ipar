<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')
    ->get('/tag/{tag_id:[0-9]+}/product', ['as'=>'admin-ui-tag-show_product', 'action' => 'Admin\Ui\TagProductController@showProduct'])
    ->get('/product/{eid:[0-9]+}/tag/add_multiple', ['as' => 'admin-ui-tag_product-add_multiple', 'action' => 'Admin\Ui\TagProductController@addTagMultiple'])
    ->post('/product/{eid:[0-9]+}/tag/add_multiple', ['as' => 'admin-ui-tag_product-add_multiple-post', 'action' => 'Admin\Ui\TagProductController@addTagMultiplePost']);