<?php

$this
->setCurrentSite('www')
->setCurrentAccess('login')


->get('/invent/create', ['as' => 'invent-create', 'action' => 'Ipar\Ui\InventController@create'])
->post('/invent/create', ['as' => 'invent-create-post', 'action' => 'Ipar\Ui\InventController@createPost'])
->get('/invent/{zcode:[0-9a-z-]+}/edit', ['as' => 'invent-edit', 'action' => 'Ipar\Ui\InventController@edit'])
->post('/invent/{zcode:[0-9a-z-]+}/edit', ['as' => 'invent-edit-post', 'action' => 'Ipar\Ui\InventController@editPost'])

->get('/invent/{zcode:[0-9a-z-]+}/delete', ['as' => 'invent-delete', 'action' => 'Ipar\Ui\InventController@delete'])
->post('/invent/{zcode:[0-9a-z-]+}/delete', ['as' => 'invent-delete-post', 'action' => 'Ipar\Ui\InventController@deletePost'])


->setCurrentAccess('public')
->get('/invent', ['as' => 'invent-index','action' => 'Ipar\Ui\InventController@index'])
->get('/invent/{zcode:[0-9a-z-]+}', ['as' => 'invent-show', 'action' => 'Ipar\Ui\InventController@show'])
->get('/invent/{zcode:[0-9a-z-]+}/summary', ['as' => 'invent-summary', 'action' => 'Ipar\Ui\InventController@summary'])
->get('/invent/{zcode:[0-9a-z-]+}/rqt', ['as' => 'invent-rqt', 'action' => 'Ipar\Ui\InventRqtController@index'])

->get('/invent/{zcode:[0-9a-z-]+}/team', ['as' => 'invent-team', 'action' => 'Ipar\Ui\InventTeamController@index'])

->setCurrentAccess('public')
->get('/invent/{zcode:[0-9a-z-]+}/team/{tid:[0-9]+}/invite', ['as' => 'invent-team-invite', 'action' => 'Ipar\Ui\InventTeamController@invite'])
->get('/invent/{zcode:[0-9a-z-]+}/team/{tid:[0-9]+}/invite-user', ['as' => 'invent-team-invite-user', 'action' => 'Ipar\Ui\InventTeamController@inviteUser'])
->get('/invent/team/join/{tid:[0-9]+}/{invite_code:[0-9a-z-]+}', ['as' => 'invent-team-join', 'action' => 'Ipar\Ui\InventTeamController@join'])

->setCurrentAccess('login')
->get('/invent/{zcode:[0-9a-z-]+}/rqt/create', ['as' => 'invent-rqt-create', 'action' => 'Ipar\Ui\InventRqtController@create'])
->post('/invent/{zcode:[0-9a-z-]+}/rqt/create', ['as' => 'invent-rqt-create-post', 'action' => 'Ipar\Ui\InventRqtController@createPost'])

->setCurrentAccess('public')
->get('/invent/{zcode:[0-9a-z-]+}/feature', ['as' => 'invent-feature', 'action' => 'Ipar\Ui\InventFeatureController@index'])
->get('/invent/{zcode:[0-9a-z-]+}/feature/{eid:[0-9]+}/show', ['as' => 'invent-feature-show', 'action' => 'Ipar\Ui\InventFeatureController@show'])

->setCurrentAccess('login')
->get('/invent/{zcode:[0-9a-z-]+}/feature/create', ['as' => 'invent-feature-create', 'action' => 'Ipar\Ui\InventFeatureController@create'])
->post('/invent/{zcode:[0-9a-z-]+}/feature/create', ['as' => 'invent-feature-create-post', 'action' => 'Ipar\Ui\InventFeatureController@createPost'])
->get('/invent/{zcode:[0-9a-z-]+}/feature/{eid:[0-9]+}/edit', ['as' => 'invent-feature-edit', 'action' => 'Ipar\Ui\InventFeatureController@edit'])
->post('/invent/{zcode:[0-9a-z-]+}/feature/{eid:[0-9]+}/edit', ['as' => 'invent-feature-edit-post', 'action' => 'Ipar\Ui\InventFeatureController@editPost'])
->get('/invent/{zcode:[0-9a-z-]+}/feature/{eid:[0-9]+}/delete', ['as' => 'invent-feature-delete', 'action' => 'Ipar\Ui\InventFeatureController@delete'])
->post('/invent/{zcode:[0-9a-z-]+}/feature/{eid:[0-9]+}/delete', ['as' => 'invent-feature-delete-post', 'action' => 'Ipar\Ui\InventFeatureController@deletePost'])


->setCurrentAccess('public')
->get('/invent/{zcode:[0-9a-z-]+}/sketch', ['as' => 'invent-sketch', 'action' => 'Ipar\Ui\InventSketchController@index'])
->get('/invent/{zcode:[0-9a-z-]+}/sketch/{eid:[0-9]+}/show', ['as' => 'invent-sketch-show', 'action' => 'Ipar\Ui\InventSketchController@show'])

->setCurrentAccess('login')
->get('/invent/{zcode:[0-9a-z-]+}/sketch/create', ['as' => 'invent-sketch-create', 'action' => 'Ipar\Ui\InventSketchController@create'])
->post('/invent/{zcode:[0-9a-z-]+}/sketch/create', ['as' => 'invent-sketch-create-post', 'action' => 'Ipar\Ui\InventSketchController@createPost'])
->get('/invent/{zcode:[0-9a-z-]+}/sketch/{eid:[0-9]+}/edit', ['as' => 'invent-sketch-edit', 'action' => 'Ipar\Ui\InventSketchController@edit'])
->post('/invent/{zcode:[0-9a-z-]+}/sketch/{eid:[0-9]+}/edit', ['as' => 'invent-sketch-edit-post', 'action' => 'Ipar\Ui\InventSketchController@editPost'])
->get('/invent/{zcode:[0-9a-z-]+}/sketch/{eid:[0-9]+}/delete', ['as' => 'invent-sketch-delete', 'action' => 'Ipar\Ui\InventSketchController@delete'])
->post('/invent/{zcode:[0-9a-z-]+}/sketch/{eid:[0-9]+}/delete', ['as' => 'invent-sketch-delete-post', 'action' => 'Ipar\Ui\InventSketchController@deletePost'])

->setCurrentAccess('public')
->get('/invent/{zcode:[0-9a-z-]+}/appearance', ['as' => 'invent-appearance', 'action' => 'Ipar\Ui\InventAppearanceController@index'])
->get('/invent/{zcode:[0-9a-z-]+}/appearance/{eid:[0-9]+}/show', ['as' => 'invent-appearance-show', 'action' => 'Ipar\Ui\InventAppearanceController@show'])

->setCurrentAccess('login')
->get('/invent/{zcode:[0-9a-z-]+}/appearance/create', ['as' => 'invent-appearance-create', 'action' => 'Ipar\Ui\InventAppearanceController@create'])
->post('/invent/{zcode:[0-9a-z-]+}/appearance/create', ['as' => 'invent-appearance-create-post', 'action' => 'Ipar\Ui\InventAppearanceController@createPost'])
->get('/invent/{zcode:[0-9a-z-]+}/appearance/{eid:[0-9]+}/edit', ['as' => 'invent-appearance-edit', 'action' => 'Ipar\Ui\InventAppearanceController@edit'])
->post('/invent/{zcode:[0-9a-z-]+}/appearance/{eid:[0-9]+}/edit', ['as' => 'invent-appearance-edit-post', 'action' => 'Ipar\Ui\InventAppearanceController@editPost'])
->get('/invent/{zcode:[0-9a-z-]+}/appearance/{eid:[0-9]+}/delete', ['as' => 'invent-appearance-delete', 'action' => 'Ipar\Ui\InventAppearanceController@delete'])
->post('/invent/{zcode:[0-9a-z-]+}/appearance/{eid:[0-9]+}/delete', ['as' => 'invent-appearance-delete-post', 'action' => 'Ipar\Ui\InventAppearanceController@deletePost']);
