<?php

$this->setCurrentSite('admin')

    ->setCurrentAccess('super')

    ->get('/activity/add-activity', ['as' => 'admin-activity-add', 'action' => 'Admin\Ui\ActivityController@addActivity'])
    ->get('/activity/edit-activity', ['as' => 'admin-activity-list-edit', 'action' => 'Admin\Ui\ActivityController@editActivity'])
    ->get('/activity/list-activity', ['as' => 'admin-activity-list', 'action' => 'Admin\Ui\ActivityController@activityList'])
    ->get('/activity/add-index-banner', ['as' => 'admin-ui-activity-add-index-banner', 'action' => 'Admin\Ui\ActivityController@addIndexBanner'])
    ->post('/activity/add', ['as' => 'admin-ui-activity-add_post', 'action' => 'Admin\Ui\ActivityController@addPost'])
    ->post('/activity/add-img', ['as' => 'admin-ui-activity-add-img', 'action' => 'Admin\Ui\ActivityController@addImg'])
    ->post('/activity/edit-activity', ['as' => 'admin-ui-activity-edit-activity', 'action' => 'Admin\Ui\ActivityController@editPost'])
    ->get('/activity/advice-index-page', ['as' => 'admin-ui-activity-advice-index-page', 'action' => 'Admin\Ui\ActivityController@adviceToIndexPage'])
    ->get('/activity/cancelAllAdviceToIndexPage', ['as' => 'admin-ui-activity-cancel-all-index-advice', 'action' => 'Admin\Ui\ActivityController@cancelAllAdviceToIndexPage'])
    ->get('/activity/delete-activity', ['as' => 'admin-ui-delete_activity', 'action' => 'Admin\Ui\ActivityController@deleteActivity']);
