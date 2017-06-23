<?php
$this
    ->setCurrentSite('api')

    ->setCurrentAccess('public')
    ->getRest('/search/associate-suggest', ['as' => 'ipar-rest-search-associate-suggest', 'action' => 'Ipar\Rest\SearchController@relatedSuggest']);
