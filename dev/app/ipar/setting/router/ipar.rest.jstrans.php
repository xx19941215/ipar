<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('public')

    ->postRest('/js/trans', ['as' => 'ipar-rest-js-trans', 'action' => 'Ipar\Rest\JsTransController@jsTransPost'])
;
