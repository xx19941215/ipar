<?php
$this->setCurrentSite('www')
    ->setCurrentAccess('public')

    ->get('/product/purchase/go/{product_purchase_id:[0-9]+}', ['as' => 'ipar-ui-product_purchase-go', 'action' => 'Ipar\Ui\ProductPurchaseController@go']);