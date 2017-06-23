<?php
$this->setCurrentSite('admin')

    ->setCurrentAccess('super')

    // product
    ->get('/product', ['as' => 'admin-product', 'action' => 'Admin\Ui\ProductController@index'])
    ->get('/product/search', ['as' => 'admin-product-search', 'action' => 'Admin\Ui\ProductController@search'])
    ->get('/product/add', ['as' => 'admin-product-add', 'action' => 'Admin\Ui\ProductController@add'])
    ->post('/product/add', ['as' => 'admin-product-add-post', 'action' => 'Admin\Ui\ProductController@addPost'])
    ->get('/product/{eid:[0-9]+}/delete', ['as' => 'admin-product-delete', 'action' => 'Admin\Ui\ProductController@delete'])
    ->post('/product/{eid:[0-9]+}/delete', ['as' => 'admin-product-delete-post', 'action' => 'Admin\Ui\ProductController@deletePost'])

    ->get('/product/{eid:[0-9]+}', ['as' => 'admin-product-show', 'action' => 'Admin\Ui\ProductController@show'])
    ->get('/product/{eid:[0-9]+}/content', ['as' => 'admin-product-content', 'action' => 'Admin\Ui\ProductController@content'])

    ->get('/product/{eid:[0-9]+}/edit', ['as' => 'admin-product-edit', 'action' => 'Admin\Ui\ProductController@edit'])
    ->post('/product/{eid:[0-9]+}/edit', ['as' => 'admin-product-edit-post', 'action' => 'Admin\Ui\ProductController@editPost'])
    ->get('/product/{eid:[0-9]+}/property', ['as' => 'admin-product-property', 'action' => 'Admin\Ui\ProductController@property'])


    ->get('/product/{eid:[0-9]+}/purchase', ['as' => 'admin-ui-product-purchase', 'action' => 'Admin\Ui\ProductPurchaseController@purchase'])
    ->get('/product/{eid:[0-9]+}/purchase/add', ['as' => 'admin-ui-product-purchase-add', 'action' => 'Admin\Ui\ProductPurchaseController@add'])
    ->post('/product/{eid:[0-9]+}/purchase/add', ['as' => 'admin-ui-product-purchase-add_post', 'action' => 'Admin\Ui\ProductPurchaseController@addPost'])
    ->get('/product/{eid:[0-9]+}/purchase/{pid:[0-9]+}/edit', ['as' => 'admin-ui-product-purchase-edit', 'action' => 'Admin\Ui\ProductPurchaseController@edit'])
    ->post('/product/{eid:[0-9]+}/purchase/{pid:[0-9]+}/edit', ['as' => 'admin-ui-product-purchase-edit_post', 'action' => 'Admin\Ui\ProductPurchaseController@editPost'])
    ->get('/product/{eid:[0-9]+}/purchase/{pid:[0-9]+}/delete', ['as' => 'admin-ui-product-purchase-delete', 'action' => 'Admin\Ui\ProductPurchaseController@delete'])
    ->post('/product/{eid:[0-9]+}/purchase/{pid:[0-9]+}/delete', ['as' => 'admin-ui-product-purchase-delete_post', 'action' => 'Admin\Ui\ProductPurchaseController@deletePost'])

    //product-feature
    ->get('/product/{eid:[0-9]+}/feature', ['as' => 'admin-product-feature', 'action' => 'Admin\Ui\ProductController@feature'])
    ->get('/product/{eid:[0-9]+}/feature/add', ['as' => 'admin-product-feature-add', 'action' => 'Admin\Ui\ProductController@addFeature'])
    ->post('/product/{eid:[0-9]+}/feature/add', ['as' => 'admin-product-feature-add-post', 'action' => 'Admin\Ui\ProductController@addFeaturePost'])

    //product-solved
    ->get('/product/{eid:[0-9]+}/solved', ['as' => 'admin-product-solved', 'action' => 'Admin\Ui\ProductController@solved'])
    ->get('/product/{eid:[0-9]+}/solved/add', ['as' => 'admin-product-solved-add', 'action' => 'Admin\Ui\ProductController@addSolved'])
    ->post('/product/{eid:[0-9]+}/solved/add', ['as' => 'admin-product-solved-add-post', 'action' => 'Admin\Ui\ProductController@addSolvedPost'])

    // product-improving
    ->get('/product/{eid:[0-9]+}/improving', ['as' => 'admin-product-improving', 'action' => 'Admin\Ui\ProductController@improving'])
    ->get('/product/{eid:[0-9]+}/improving/add', ['as' => 'admin-product-improving-add', 'action' => 'Admin\Ui\ProductController@addImproving'])
    ->post('/product/{eid:[0-9]+}/improving/add', ['as' => 'admin-product-improving-add-post', 'action' => 'Admin\Ui\ProductController@addImprovingPost'])

    // product-pbranch
    ->get('/product/{eid:[0-9]+}/pbranch', ['as' => 'admin-product-pbranch', 'action' => 'Admin\Ui\ProductController@pbranch'])
    ->get('/product/{eid:[0-9]+}/pbranch/add', ['as' => 'admin-product-pbranch-add', 'action' => 'Admin\Ui\ProductController@addPbranch'])
    ->post('/product/{eid:[0-9]+}/pbranch/add', ['as' => 'admin-product-pbranch-add-post', 'action' => 'Admin\Ui\ProductController@addPbranchPost'])
    ->get('/product/{eid:[0-9]+}/pbranch/{pbranch_id:[0-9]+}/edit', ['as' => 'admin-product-pbranch-edit', 'action' => 'Admin\Ui\ProductController@editPbranch'])
    ->post('/product/{eid:[0-9]+}/pbranch/{pbranch_id:[0-9]+}/edit', ['as' => 'admin-product-pbranch-edit-post', 'action' => 'Admin\Ui\ProductController@editPbranchPost'])

    // product-ptag
    ->get('/product/{eid:[0-9]+}/ptag', ['as' => 'admin-product-ptag', 'action' => 'Admin\Ui\ProductController@ptag'])
    ->get('/product/{eid:[0-9]+}/ptag/add', ['as' => 'admin-product-ptag-add', 'action' => 'Admin\Ui\ProductController@addPtag'])
    ->post('/product/{eid:[0-9]+}/ptag/add', ['as' => 'admin-product-ptag-add-post', 'action' => 'Admin\Ui\ProductController@addPtagPost'])
    ->get('/product/{eid:[0-9]+}/ptag/{ptag_id:[0-9]+}/edit', ['as' => 'admin-product-ptag-edit', 'action' => 'Admin\Ui\ProductController@editPtag'])
    ->post('/product/{eid:[0-9]+}/ptag/{ptag_id:[0-9]+}/edit', ['as' => 'admin-product-ptag-edit-post', 'action' => 'Admin\Ui\ProductController@editPtagPost'])

    // product-ptarget
    ->get('/product/{eid:[0-9]+}/ptarget', ['as' => 'admin-product-ptarget', 'action' => 'Admin\Ui\ProductController@ptarget'])
    ->get('/product/{eid:[0-9]+}/ptarget/add', ['as' => 'admin-product-ptarget-add', 'action' => 'Admin\Ui\ProductController@addPtarget'])
    ->post('/product/{eid:[0-9]+}/ptarget/add', ['as' => 'admin-product-improving-add-post', 'action' => 'Admin\Ui\ProductController@addPtargetPost'])
    ->get('/product/{eid:[0-9]+}/ptarget/{ptarget_id:[0-9]+}/edit', ['as' => 'admin-product-ptarget-edit', 'action' => 'Admin\Ui\ProductController@editPtarget'])
    ->post('/product/{eid:[0-9]+}/ptarget/{ptarget_id:[0-9]+}/edit', ['as' => 'admin-product-ptarget-edit-post', 'action' => 'Admin\Ui\ProductController@editPtargetPost'])

    //product-company
    ->get('/product/{eid:[0-9]+}/company',['as' => 'admin-ui-product_company','action' => 'Admin\Ui\ProductCompanyController@list'])
    ->get('/product/{eid:[0-9]+}/company/link',['as' => 'admin-ui-product_company-link','action' => 'Admin\Ui\ProductCompanyController@link'])
    ->post('/product/{eid:[0-9]+}/company/link',['as' => 'admin-ui-product_company-link_post','action' => 'Admin\Ui\ProductCompanyController@linkPost'])
    ->get('/product/{eid:[0-9]+}/company/{gid:[0-9]+}/unlink',['as' => 'admin-ui-product_company-unlink','action' => 'Admin\Ui\ProductCompanyController@unlink'])
    ->post('/product/{eid:[0-9]+}/company/{gid:[0-9]+}/unlink',['as' => 'admin-ui-product_company-unlink_post','action' => 'Admin\Ui\ProductCompanyController@unlinkPost'])
    //brand-tag
    ->get('/product/{eid:[0-9]+}/brand-tag',['as' => 'admin-ui-product_brand_tag-show','action' => 'Admin\Ui\ProductBrandTagController@show']);
