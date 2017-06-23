<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('login')

    ->postRest('/address/fetchAreas', ['as' => 'api-address-fetchAreas', 'action' => 'Ipar\Rest\AddressController@fetchAreas']);
