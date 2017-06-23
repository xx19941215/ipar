<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')

    ->get('/industry_tag/{tag_id:[0-9]+}', ['as' => 'admin-ui-industry_tag-show', 'action' => 'Admin\Ui\IndustryTagCompanyController@show'])
    ->get('/industry_tag/{tag_id:[0-9]+}/company', ['as' => 'admin-ui-industry_tag_company-list', 'action' => 'Admin\Ui\IndustryTagCompanyController@list'])

    ->get('/industry_tag/{tag_id:[0-9]+}/company/{gid:[0-9]+}/unlink', ['as' => 'admin-ui-industry_tag_company-unlink', 'action' => 'Admin\Ui\IndustryTagCompanyController@unlink'])
    ->post('/industry_tag/{tag_id:[0-9]+}/company/{gid:[0-9]+}/unlink', ['as' => 'admin-ui-industry_tag_company-unlink_post', 'action' => 'Admin\Ui\IndustryTagCompanyController@unlinkPost']);
