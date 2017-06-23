<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('login')


    ->postRest('/img/upload-figure', ['as' => 'ipar-rest-img-upload_figure_post', 'action' => 'Ipar\Rest\ImgController@uploadFigurePost'])
    ->postRest('/img/retrieve-image', ['as' => 'img-retrieve', 'action' => 'Ipar\Rest\ImgController@retrieveImagePost']);


    //->postRest('/img/upload-avatar', ['as' => 'img-upload-avatar', 'action' => 'ImgController@uploadAvatarPost'])
