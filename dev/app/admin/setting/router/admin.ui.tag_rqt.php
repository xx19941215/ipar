<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')

    ->get('/tag/{tag_id:[0-9]+}/rqt', ['as'=>'admin-ui-tag-show_rqt', 'action' => 'Admin\Ui\TagRqtController@searchRqt'])
    ->get('/rqt/{eid:[0-9]+}/tag/add_multiple', ['as' => 'admin-ui-tag_rqt-add_multiple', 'action' => 'Admin\Ui\TagRqtController@addTagMultiple'])
    ->post('/rqt/{eid:[0-9]+}/tag/add_multiple', ['as' => 'admin-ui-tag_rqt-add_multiple-post', 'action' => 'Admin\Ui\TagRqtController@addTagMultiplePost']);