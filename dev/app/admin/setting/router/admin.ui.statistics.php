<?php
$this->setCurrentSite('admin')
->setCurrentAccess('super')

->get('/statistics/list', ['as' => 'admin-statistics-list', 'action' => 'Admin\Ui\StatisticsController@list'])
->get('/statistics/unsolved-product-list', ['as' => 'admin-statistics-unsolved_product_list',
    'action' => 'Admin\Ui\StatisticsController@unsolvedProductList'])
->get('/statistics/unasso-rqt-list', ['as' => 'admin-statistics-unasso_rqt_list',
    'action' => 'Admin\Ui\StatisticsController@unassoRqtList']);
