<?php
$this->setCurrentSite('i')
    ->setCurrentAccess('login')
    ->get('/', ['as' => 'ipar-i-index', 'action' => 'Ipar\Ui\IController@index'])
    //->post('/img/upload-avt', ['as' => 'img-upload-avt', 'action' => 'Ipar\IController@uploadAvtPost'])

    ->setCurrentAccess('public')
    ->get('/{zcode:[0-9a-z-]+}', ['as' => 'ipar-i-home', 'action' => 'Ipar\Ui\IController@home'])
    ->get('/{zcode:[0-9a-z-]+}/rqt', ['as' => 'ipar-i-rqt', 'action' => 'Ipar\Ui\IController@rqt'])
    ->get('/{zcode:[0-9a-z-]+}/feature', ['as' => 'ipar-i-feature', 'action' => 'Ipar\Ui\IController@feature'])
    ->get('/{zcode:[0-9a-z-]+}/product', ['as' => 'ipar-i-product', 'action' => 'Ipar\Ui\IController@product']);
    /*
    ->get('/{zcode:[0-9a-z-]+}/rqt', ['as' => 'i-rqt', 'action' => 'Ipar\IController@rqt'])
    ->get('/{zcode:[0-9a-z-]+}/idea', ['as' => 'i-idea', 'action' => 'Ipar\IController@idea'])
    ->get('/{zcode:[0-9a-z-]+}/invent', ['as' => 'i-invent', 'action' => 'Ipar\IController@invent'])
    ->get('/{zcode:[0-9a-z-]+}/feature', ['as' => 'i-feature', 'action' => 'Ipar\IController@feature'])
    ->get('/{zcode:[0-9a-z-]+}/sketch', ['as' => 'i-sketch', 'action' => 'Ipar\IController@sketch'])
    ->get('/{zcode:[0-9a-z-]+}/appearance', ['as' => 'i-appearance', 'action' => 'Ipar\IController@appearance']);
     */
