<?php

$base_host = 'ideapar.do';
$base_dir = $this->get('base_dir');

$this
    ->set('debug', true)
    ->set('base_host', $base_host)

    ->set('local', [
        'db_default_host' => '127.0.0.1',
        'db_default_database' => 'ideapar',
        'db_default_username' => 'ideapar',
        'db_default_password' => '123456789',

        'db_i18n_host' => '127.0.0.1',
        'db_i18n_database' => 'ideapar',
        'db_i18n_username' => 'ideapar',
        'db_i18n_password' => '123456789',

        'cache_default_host' => '127.0.0.1',
        'cache_i18n_host' => '127.0.0.1',
        'cache_session_host' => '127.0.0.1',

        'site_front_host' => 'front.' . $base_host,
        'site_front_dir' => $base_dir . '/front',

        'site_static_host' => 'static.' . $base_host,
        'site_static_dir' => $base_dir . '/static',

    ]);
