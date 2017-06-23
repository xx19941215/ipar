<?php

$this->setCurrentSite('admin')

    ->setCurrentAccess('super')

    ->get('/activity-video/config-video', ['as' => 'admin-ui-config_video', 'action' => 'Admin\Ui\ActivityVideoController@configVideo'])
    ->post('/activity-video/post-config-video', ['as' => 'admin-ui-post-config_video', 'action' => 'Admin\Ui\ActivityVideoController@postConfigVideo']);
