<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('public')

    ->getRest('/search_company', ['as' => 'ipar-rest-company-search_company', 'action' => 'Ipar\Rest\CompanyController@search']);
