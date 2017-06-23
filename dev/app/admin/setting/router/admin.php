<?php
$this->setCurrentSite('admin')
->setCurrentAccess('super')
->get('/', ['as' => 'admin-home', 'action' => 'Admin\Ui\IndexController@home'])

->get('/lang', ['as' => 'admin-lang', 'action' => 'Admin\Ui\LangController@index'])
->post('/lang/save', ['as' => 'admin-lang-save-post', 'action' => 'Admin\Ui\LangController@savePost'])

->get('/cache/flush-all', ['as' => 'admin-cache-flush-all', 'action' => 'Admin\Ui\CacheController@flushAll'])

->get('/trans', ['as' => 'admin-trans', 'action' => 'Admin\Ui\TransController@index'])
->get('/trans/add', ['as' => 'admin-trans-add', 'action' => 'Admin\Ui\TransController@add'])
->post('/trans/add', ['as' => 'admin-trans-add-post', 'action' => 'Admin\Ui\TransController@addPost'])

->get('/trans/{id:[0-9]+}/edit', ['as' => 'admin-trans-edit', 'action' => 'Admin\Ui\TransController@edit'])
->post('/trans/{id:[0-9]+}/edit', ['as' => 'admin-trans-edit-post', 'action' => 'Admin\Ui\TransController@editPost'])

->get('/trans/{id:[0-9]+}/delete', ['as' => 'admin-trans-delete', 'action' => 'Admin\Ui\TransController@delete'])
->post('/trans/{id:[0-9]+}/delete', ['as' => 'admin-trans-delete-post', 'action' => 'Admin\Ui\TransController@deletePost']);
