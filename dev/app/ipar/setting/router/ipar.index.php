<?php
$this->setCurrentSite('www')
    ->setCurrentAccess('public')
    ->get('/', ['as' => 'home', 'action' => 'Ipar\Ui\IndexController@home'])
    ->get('/search', ['as' => 'ipar-index-search', 'action' => 'Ipar\Ui\IndexController@search'])
    ->get('/search/article', ['as' => 'ipar-index-search-article', 'action' => 'Ipar\Ui\SearchArticleController@search'])
    ->get('/search/rqt', ['as' => 'ipar-index-search-rqt', 'action' => 'Ipar\Ui\SearchRqtController@search'])
    ->get('/search/user', ['as' => 'ipar-index-search-user', 'action' => 'Ipar\Ui\SearchUserController@search'])
    ->get('/search/product', ['as' => 'ipar-index-search-product', 'action' => 'Ipar\Ui\SearchProductController@search'])
    ->get('/search/company', ['as' => 'ipar-index-search-company', 'action' => 'Ipar\Ui\SearchCompanyController@search']);
