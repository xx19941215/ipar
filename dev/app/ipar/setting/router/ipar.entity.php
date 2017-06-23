<?php
$this->setCurrentSite('www')
    ->setCurrentAccess('public')

    ->get('/entity', ['as' => 'ipar-entity-index', 'action' => 'Ipar\Ui\EntityController@index']);
    //->get('/search', ['as' => 'search', 'action' => 'Ipar\Ui\IndexController@search']);
