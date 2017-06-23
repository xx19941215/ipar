<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')


    ->get('/industry_tag', ['as' => 'admin-ui-industry_tag-index', 'action' => 'Admin\Ui\IndustryTagController@list'])

    ->get('/industry_tag/add', ['as' => 'admin-ui-industry_tag-add', 'action' => 'Admin\Ui\IndustryTagController@add'])
    ->post('/industry_tag/add', ['as' => 'admin-ui-industry_tag-add_post', 'action' => 'Admin\Ui\IndustryTagController@addPost'])

    ->get('/industry_tag/{tag_id:[0-9]+}/delete', ['as' => 'admin-ui-industry_tag-delete', 'action' => 'Admin\Ui\IndustryTagController@delete'])
    ->post('/industry_tag/{tag_id:[0-9]+}/delete', ['as' => 'admin-ui-industry_tag-delete_post', 'action' => 'Admin\Ui\IndustryTagController@deletePost']);
