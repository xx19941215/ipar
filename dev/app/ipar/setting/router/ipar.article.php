<?php
$this
    ->setCurrentSite('www')

    ->setCurrentAccess('public')
    ->get('/article', ['as' => 'ipar-article-index', 'action' => 'Ipar\Ui\ArticleController@index'])
    ->get('/article/{zcode:[0-9a-z-]+}', ['as' => 'ipar-article-show', 'action' => 'Ipar\Ui\ArticleController@show']);

?>
