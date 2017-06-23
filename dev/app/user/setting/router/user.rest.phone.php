<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('public')
    ->postRest('/mobile-check', ['as' => 'user-rest-phone-check', 'action' => 'User\Rest\AuthMobileController@phoneCheck'])
    ->postRest('/nick-check', ['as' => 'user-rest-nick-check', 'action' => 'User\Rest\AuthMobileController@nickCheck'])
    ->postRest('/send-code', ['as' => 'user-rest-phone-send-code', 'action' => 'User\Rest\AuthMobileController@send'])
    ->postRest('/mobile-validate-check', ['as' => 'user-rest-phone-validate-check', 'action' => 'User\Rest\AuthMobileController@phoneValidateCheck']);

