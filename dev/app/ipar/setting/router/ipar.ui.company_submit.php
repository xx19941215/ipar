<?php
$this->setCurrentSite('page')
     ->setCurrentAccess('public')
     ->get('/company-submit', ['as' => 'ipar-company-submit', 'action' => 'Ipar\Ui\CompanySubmitController@submitCompanyMsg']);
