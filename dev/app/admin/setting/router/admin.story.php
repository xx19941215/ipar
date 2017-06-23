<?php

$this->setCurrentSite('admin')

    ->setCurrentAccess('super')

    // story
    ->get('/story', ['as' => 'admin-story', 'action' => 'Admin\Ui\StoryController@index'])
    ->get('/story/{id:[0-9]+}', ['as' => 'admin-story-show', 'action' => 'Admin\Ui\StoryController@show']);
