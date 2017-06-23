<?php

$base_host = $this->get('base_host');
$base_dir = $this->get('base_dir');

$site_static_host = $this->get('local.site_static_host');
$site_static_dir = $this->get('local.site_static_dir');


$this
    ->set('privilege', [
        'super' => 100,
        'admin' => 180,
        'root' => 213,
    ])
    ->set('progress', [
        '1' => 'start',
        '2' => 'end',
    ])
    ->set('site', [
        /*
        'www' => [
            'host' => 'www.'.$base_host,
        ],
        'login' => [
            'host' => 'login.'.$base_host,
        ],
        'logout' => [
            'host' => 'logout.'.$base_host,
        ],
        'reg' => [
            'host' => 'reg.'.$base_host,
        ],
        'safe' => [
            'host' => 'safe.'.$base_host,
        ],
        'admin' => [
            'host' => 'admin.'.$base_host,
        ],
        'i' => [
            'host' => 'i.'.$base_host,
        ],
        'wx' => [
            'host' => 'wx.'.$base_host,
        ],
         */
        'api' => [
            'host' => 'api.'.$base_host,
        ],
        'front' => [
            'host' => $this->get('local.site_front_host'),
            'dir' => $this->get('local.site_front_dir'),
        ],
        'static' => [
            'host' => $site_static_host,
            'dir' => $site_static_dir,
        ],
    ])
    ->set('cache', [
        'default' => [
            'host' => $this->get('local.cache_default_host'),
            'database' => 1,
        ],
        'i18n' => [
            'host' => $this->get('local.cache_i18n_host'),
            'database' => 3,
        ],
    ])
    ->set('trace', [
        'level' => 3,
        'index' => 3
    ])
    ->set('img', [
        'site' => 'static',
        'base_dir' => '/upload/img',
        //'base_url' => 'http://'.$site_static_host.'/upload/img',
        //'base_dir' => $site_static_dir.'/public/upload/img',
    ])
    ->set('view', [
        'folders' => [$base_dir.'/main/views/foil/tpl'],
    ])
    ->set('mail', [
        'sendcloud' => [
            'api_user' => 'ant_send_ideapar',
            'api_key' => 'BGWAY4HLOGWHfhiF',
            'from' => 'no-reply@send.ideapar.com',
        ],
    ]);
