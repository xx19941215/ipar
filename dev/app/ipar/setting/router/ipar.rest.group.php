<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('login')

    ->postRest('/group/logo', ['as' => 'ipar-rest-group-logo', 'action' => 'Ipar\Rest\GroupController@uploadGroupLogo'])
    ->postRest('/group/updatelogo', ['as' => 'ipar-rest-group-logo-update', 'action' => 'Ipar\Rest\GroupController@updateGroupLogo'])


    ->postRest('/group/contact/create', ['as' => 'ipar-rest-group-contact-create', 'action' => 'Ipar\Rest\GroupController@createContactPost'])
    ->postRest('/group/contact/update', ['as' => 'ipar-rest-group-contact-update', 'action' => 'Ipar\Rest\GroupController@updateContactPost'])
    ->postRest('/group/contact/delete', ['as' => 'ipar-rest-group-contact-delete', 'action' => 'Ipar\Rest\GroupController@deleteContactPost'])


    ->postRest('/group/office/create', ['as' => 'ipar-rest-group-office-create', 'action' => 'Ipar\Rest\GroupController@createOfficePost'])
    ->postRest('/group/office/update', ['as' => 'ipar-rest-group-office-update', 'action' => 'Ipar\Rest\GroupController@updateOfficePost'])
    ->postRest('/group/office/delete', ['as' => 'ipar-rest-group-office-delete', 'action' => 'Ipar\Rest\GroupController@deleteOfficePost'])


    ->postRest('/group/social/edit', ['as' => 'ipar-rest-group-social-edit', 'action' => 'Ipar\Rest\GroupController@editSocialPost'])

    ->setCurrentAccess('public')
    ->getRest('/group', ['as' => 'ipar-rest-group-index', 'action' => 'Ipar\Rest\GroupController@index'])
;
