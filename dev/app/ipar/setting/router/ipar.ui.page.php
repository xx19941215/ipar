<?php
$this->setCurrentSite('page')
     ->setCurrentAccess('public')
     ->get('/about-us',['as' => 'ipar-ui-page-about_us', 'action' => 'Ipar\Ui\PageController@aboutUs'])
     ->get('/join-us',['as' => 'ipar-ui-page-join_us', 'action' => 'Ipar\Ui\PageController@joinUs'])

     ->get('/company-service',['as' => 'ipar-ui-company_service-show', 'action' => 'Ipar\Ui\CompanyServiceController@show'])

     ->get('/activity/bike',['as' => 'ipar-ui-page-activity_bike', 'action' => 'Ipar\Ui\PageController@bike'])
     ->get('/links',['as' => 'ipar-ui-page-links', 'action' => 'Ipar\Ui\PageController@links'])
    ->get('/activity/umbrella',['as' => 'ipar-ui-page-activity_foundation', 'action' => 'Ipar\Ui\PageController@umbrella']);

?>
