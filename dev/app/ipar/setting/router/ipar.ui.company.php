<?php
$this->setCurrentSite('www')
    ->setCurrentAccess('public')

    ->get('/company', ['as' => 'ipar-ui-company-index', 'action' => 'Ipar\Ui\CompanyController@index'])
    ->get('/company/add', ['as' => 'ipar-company-add', 'action' => 'Ipar\Ui\CompanyController@add'])
    ->post('/company/add', ['as' => 'ipar-company-add-post', 'action' => 'Ipar\Ui\CompanyController@addPost'])
    ->get('/company/{zcode:[A-z0-9]+}', ['as' => 'ipar-ui-company-show', 'action' => 'Ipar\Ui\CompanyController@show']);
