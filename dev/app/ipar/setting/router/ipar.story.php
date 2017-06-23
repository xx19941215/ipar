<?php
$this->setCurrentSite('www')
    ->setCurrentAccess('public')
    //->get('/', ['as' => 'ipar-index-home', 'action' => 'Ipar\Ui\IndexController@home']);
    ->get('/story/rqt', ['as' => 'ipar-story-rqt', 'action' => 'Ipar\Ui\StoryController@rqt'])
    ->get('/story/product', ['as' => 'ipar-story-product', 'action' => 'Ipar\Ui\StoryController@product'])
    ->get('/story', ['as' => 'ipar-story-index', 'action' => 'Ipar\Ui\StoryController@index']);
