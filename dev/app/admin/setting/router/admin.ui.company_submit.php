<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')


    ->get('/company-submit/{id:[0-9]+}', ['as' => 'admin-ui-company-submit-detail', 'action' => 'Admin\Ui\CompanySubmitController@detail'])
    ->get('/company-submit/addmark', ['as' => 'admin-ui-company-submit-addmark', 'action' => 'Admin\Ui\CompanySubmitController@addmark'])
    ->get('/company-submit/ignore', ['as' => 'admin-ui-company-submit-status', 'action' => 'Admin\Ui\CompanySubmitController@status'])
    ->get('/company-submit/search', ['as' => 'admin-ui-company-submit-search', 'action' => 'Admin\Ui\CompanySubmitController@search'])
    ->get('/company-submit/delete/{id:[0-9]+}', ['as' => 'admin-ui-company-submit-delete', 'action' => 'Admin\Ui\CompanySubmitController@delete'])
    ->get('/company-submit', ['as' => 'admin-ui-company-submit-index', 'action' => 'Admin\Ui\CompanySubmitController@index']);
