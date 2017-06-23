<?php
$this
    ->setCurrentSite('api')

    ->setCurrentAccess('public')
    ->getRest('/story', ['as' => 'ipar-rest-story-index', 'action' => 'Ipar\Rest\StoryController@index']);
