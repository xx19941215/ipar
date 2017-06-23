<?php

$this->setCurrentSite('admin')

    ->setCurrentAccess('super')

    // entity
    ->get('/entity', ['as' => 'admin-entity-index', 'action' => 'Admin\Ui\EntityController@index'])
    ->get('/entity/search', ['as' => 'admin-entity-search', 'action' => 'Admin\Ui\EntityController@search'])

    ->get('/entity/{eid:[0-9]+}/activate', ['as' => 'admin-entity-activate', 'action' => 'Admin\Ui\EntityController@activate'])
    ->post('/entity/{eid:[0-9]+}/activate', ['as' => 'admin-entity-activate-post', 'action' => 'Admin\Ui\EntityController@activatePost'])
    ->get('/entity/{eid:[0-9]+}/deactivate', ['as' => 'admin-entity-deactivate', 'action' => 'Admin\Ui\EntityController@deactivate'])
    ->post('/entity/{eid:[0-9]+}/deactivate', ['as' => 'admin-entity-deactivate-post', 'action' => 'Admin\Ui\EntityController@deactivatePost'])

    ->get('/entity/{eid:[0-9]+}/submit', ['as' => 'admin-entity-submit', 'action' => 'Admin\Ui\EntityController@submit'])

    ->get('/entity/{eid:[0-9]+}/delete', ['as' => 'admin-entity-delete', 'action' => 'Admin\Ui\EntityController@delete', 'access' => 'admin'])
    ->post('/entity/{eid:[0-9]+}/delete', ['as' => 'admin-entity-delete-post', 'action' => 'Admin\Ui\EntityController@deletePost', 'access' => 'admin']);

