<?php

$this->setCurrentSite('admin')

    ->setCurrentAccess('super')

    ->get('/activity-product/select-product', ['as' => 'admin-activity-select-product', 'action' => 'Admin\Ui\ActivityProductController@selectProduct'])
    ->get('/activity-product/edit-product', ['as' => 'admin-activity-edit', 'action' => 'Admin\Ui\ActivityProductController@editProduct'])
    ->get('/activity-product/add-product', ['as' => 'admin-ui-activity-add-product', 'action' => 'Admin\Ui\ActivityProductController@addProduct'])
    ->get('/activity-product/delete-product', ['as' => 'admin-ui-activity-delete-product', 'action' => 'Admin\Ui\ActivityProductController@deleteProduct'])
    ->get('/activity-product/advice-product', ['as' => 'admin-ui-activity-advice-product', 'action' => 'Admin\Ui\ActivityProductController@adviceProduct'])
    ->get('/activity-product/cancel-advice-product', ['as' => 'admin-ui-activity-cancel-advice-product', 'action' => 'Admin\Ui\ActivityProductController@cancelAdviceProduct'])
    ->post('/activity-product/change-product-img', ['as' => 'admin-ui-activity-change-product-img', 'action' => 'Admin\Ui\ActivityProductController@changeProductImg'])
    ->post('/activity-product/search-product', ['as' => 'admin-ui-activity-search-product', 'action' => 'Admin\Ui\ActivityProductController@searchProduct'])
    ->get('/activity-product/list-product', ['as' => 'admin-ui-activity-list-product', 'action' => 'Admin\Ui\ActivityProductController@listProduct'])
    ->get('/activity-product/active-product', ['as' => 'admin-ui-active_product', 'action' => 'Admin\Ui\ActivityProductController@activeProduct'])
    ->get('/activity-product/deactive-product', ['as' => 'admin-ui-deactive_product', 'action' => 'Admin\Ui\ActivityProductController@deActiveProduct']);
