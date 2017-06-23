<?php
$this->setCurrentSite('admin')
->setCurrentAccess('super')

->get('/article', ['as' => 'admin-article-index', 'action' => 'Admin\Ui\ArticleController@index'])
->get('/article/add', ['as' => 'admin-article-add', 'action' => 'Admin\Ui\ArticleController@add'])
->get('/article/add/{original_id:[0-9]+}', ['as' => 'admin-article-add-locale', 'action' => 'Admin\Ui\ArticleController@addLocale'])
->post('/article/add', ['as' => 'admin-article-add-post', 'action' => 'Admin\Ui\ArticleController@addPost'])
->post('/article/add/{original_id:[0-9]+}', ['as' => 'admin-article-add-locale-post', 'action' => 'Admin\Ui\ArticleController@addPost'])

->get('/article/{zcode:[0-9a-z-]+}', ['as' => 'admin-article-show', 'action' => 'Admin\Ui\ArticleController@show'])
->post('/article/{zcode:[0-9a-z-]+}', ['as' => 'admin-article-edit-post', 'action' => 'Admin\Ui\ArticleController@edit'])

->get('/article/{zcode:[0-9a-z-]+}/deactivate', ['as' => 'admin-article-deactivate', 'action' => 'Admin\Ui\ArticleController@deactivate'])
->post('/article/{zcode:[0-9a-z-]+}/deactivate', ['as' => 'admin-article-deactivate-post', 'action' => 'Admin\Ui\ArticleController@deactivatePost'])
->get('/article/{zcode:[0-9a-z-]+}/activate', ['as' => 'admin-article-activate', 'action' => 'Admin\Ui\ArticleController@activate'])
->post('/article/{zcode:[0-9a-z-]+}/activate', ['as' => 'admin-article-activate-post', 'action' => 'Admin\Ui\ArticleController@activatePost'])

->get('/article/{zcode:[0-9a-z-]+}/delete', ['as' => 'admin-article-delete', 'action' => 'Admin\Ui\ArticleController@delete'])
->post('/article/{zcode:[0-9a-z-]+}/delete', ['as' => 'admin-article-delete-post', 'action' => 'Admin\Ui\ArticleController@deletePost'])

->get('/article/{zcode:[0-9a-z-]+}/delete-locale', ['as' => 'admin-article-delete-locale', 'action' => 'Admin\Ui\ArticleController@deleteLocale'])
->post('/article/{zcode:[0-9a-z-]+}/delete-locale', ['as' => 'admin-article-delete-locale-post', 'action' => 'Admin\Ui\ArticleController@deleteLocalePost'])


?>
