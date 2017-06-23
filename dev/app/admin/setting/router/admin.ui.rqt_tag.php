<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')
    ->get('/rqt/{eid:[0-9]+}/tag', ['as' => 'admin-ui-rqt_tag-search', 'action' => 'Admin\Ui\RqtTagController@search'])
    ->get('/rqt/{eid:[0-9]+}/tag/add', ['as' => 'admin-ui-rqt_tag-add', 'action' => 'Admin\Ui\RqtTagController@addTag'])
    ->post('/rqt/{eid:[0-9]+}/tag/add', ['as' => 'admin-ui-rqt_tag-add-post', 'action' => 'Admin\Ui\RqtTagController@addTagPost'])
    ->get('/rqt/{eid:[0-9]+}/tag/{tag_id:[0-9]+}/unlink', ['as' => 'admin-ui-rqt_tag-unlink', 'action' => 'Admin\Ui\RqtTagController@unlink'])
    ->post('/rqt/{eid:[0-9]+}/tag/{tag_id:[0-9]+}/unlink', ['as' => 'admin-ui-rqt_tag-unlink-post', 'action' => 'Admin\Ui\RqtTagController@unlinkPost']);
