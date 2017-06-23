<?php
$this
    ->setCurrentSite('api')

    ->setCurrentAccess('login')
    ->postRest('/idea/save', ['as' => 'ipar-rest-idea-save-post', 'action' => 'Ipar\Rest\IdeaController@savePost']);

