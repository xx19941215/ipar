<?php
$dir_base = $this->get('dir.base');
$dir_sites = realpath($dir_base . '/../');

$host_base = 'milky.way';

$this
    ->set('debug', true)
    ->set('host', [
        'db' => 'mariadb',
        'cache' => 'redis',

        'base' => $host_base,

        'static' => 'static.' . $host_base,
        'prototype' => 'prototype.' . $host_base,
        'front' => 'front.' .$host_base
    ])

    ->set('dir', [
        'base' => $dir_base,
        'static' => $dir_sites . '/static/public',
        'prototype' => $dir_sites . '/prototype/public',
        'front' => $dir_sites . '/front/public',
    ])

    ->set('lang', [
        'db' => [
            'database' => 'ipar3',
            'username' => 'ipar3',
            'password' => '123456789'
        ]
    ])
    ->set('db', [
        'default' => [
            'database' => 'ipar3',
            'username' => 'ipar3',
            'password' => '123456789',
        ]
    ]);

