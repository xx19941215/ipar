<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('public')
    ->postRest('/email-validate-check', ['as' => 'user-rest-email-validate-check', 'action' => 'User\Rest\AuthEmailController@EmailValidateCheck'])
    ->postRest('/send-email-code', ['as' => 'user-rest-email-send-code', 'action' => 'User\Rest\AuthEmailController@send']);

