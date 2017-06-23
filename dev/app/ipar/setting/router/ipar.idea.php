<?php
$this
->setCurrentSite('www')
->setCurrentAccess('public')

->get('/idea', ['as' => 'ipar-idea-index','action' => 'Ipar\Ui\IdeaController@index'])
->get('/idea/{zcode:[0-9a-z-]+}', ['as' => 'ipar-idea-show', 'action' => 'Ipar\Ui\IdeaController@show'])

->setCurrentAccess('login')
->get('/idea/{zcode:[0-9a-z-]+}/edit', ['as' => 'ipar-idea-edit', 'action' => 'Ipar\Ui\IdeaController@edit'])
->post('/idea/{zcode:[0-9a-z-]+}/edit', ['as' => 'ipar-idea-edit-post', 'action' => 'Ipar\Ui\IdeaController@editPost'])

->get('/idea/{zcode:[0-9a-z-]+}/delete', ['as' => 'ipar-idea-delete', 'action' => 'Ipar\Ui\IdeaController@delete'])
->post('/idea/{zcode:[0-9a-z-]+}/delete', ['as' => 'ipar-idea-delete-post', 'action' => 'Ipar\Ui\IdeaController@deletePost']);

