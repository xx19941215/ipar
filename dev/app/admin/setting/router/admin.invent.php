<?php
$this->setCurrentSite('admin')
    ->setCurrentAccess('super')


    //event
    ->get('/event', ['as' => 'admin-event', 'action' => 'Admin\Ui\EventController@index'])
    ->get('/event/{id:[0-9]+}/show', ['as' => 'admin-event-show', 'action' => 'Admin\Ui\EventController@show'])

    // invent
    ->get('/rqt/{eid:[0-9]+}/invent', ['as' => 'admin-rqt-invent', 'action' => 'Admin\Ui\RqtController@invent'])
    ->get('/rqt/{eid:[0-9]+}/invent/add', ['as' => 'admin-rqt-invent-add', 'action' => 'Admin\Ui\RqtController@addInvent'])
    ->post('/rqt/{eid:[0-9]+}/invent/add', ['as' => 'admin-rqt-invent-add-post', 'action' => 'Admin\Ui\RqtController@addInventPost'])

    ->get('/invent/{eid:[0-9]+}/show', ['as' => 'admin-invent-show', 'action' => 'Admin\Ui\InventController@show'])
    ->get('/invent/{eid:[0-9]+}/edit', ['as' => 'admin-invent-edit', 'action' => 'Admin\Ui\InventController@edit'])
    ->post('/invent/{eid:[0-9]+}/edit', ['as' => 'admin-invent-edit-post', 'action' => 'Admin\Ui\InventController@editPost'])
    ->get('/invent/{eid:[0-9]+}/delete', ['as' => 'admin-invent-delete', 'action' => 'Admin\Ui\InventController@delete'])
    ->post('/invent/{eid:[0-9]+}/delete', ['as' => 'admin-invent-delete-post', 'action' => 'Admin\Ui\InventController@deletePost']);
