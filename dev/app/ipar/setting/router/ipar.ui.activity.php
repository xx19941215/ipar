<?php
$this->setCurrentSite('page')
     ->setCurrentAccess('public')

     ->get('/activity/index', ['as' => 'ipar-ui-page-activity-index', 'action' => 'Ipar\Ui\ActivityController@index']);
