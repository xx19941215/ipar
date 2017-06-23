<?php
$this->setCurrentSite('admin')
    ->setCurrentAccess('super')

    // idea
    ->get('/idea', ['as' => 'admin-idea', 'action' => 'Admin\Ui\IdeaController@index'])
    ->get('/idea/{eid:[0-9]+}', ['as' => 'admin-idea-show', 'action' => 'Admin\Ui\IdeaController@show'])
    ->get('/idea/{eid:[0-9]+}/content', ['as' => 'admin-idea-content', 'action' => 'Admin\Ui\IdeaController@content'])
    ->get('/idea/{eid:[0-9]+}/edit', ['as' => 'admin-idea-edit', 'action' => 'Admin\Ui\IdeaController@edit'])
    ->post('/idea/{eid:[0-9]+}/edit', ['as' => 'admin-idea-edit-post', 'action' => 'Admin\Ui\IdeaController@editPost'])
    ->get('/idea/{eid:[0-9]+}/delete', ['as' => 'admin-idea-delete', 'action' => 'Admin\Ui\IdeaController@delete'])
    ->post('/idea/{eid:[0-9]+}/delete', ['as' => 'admin-idea-delete-post', 'action' => 'Admin\Ui\IdeaController@deletePost']);



