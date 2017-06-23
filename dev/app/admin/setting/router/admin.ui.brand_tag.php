<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')


    ->get('/brand_tag', ['as' => 'admin-ui-brand_tag-index', 'action' => 'Admin\Ui\BrandTagController@list']);
