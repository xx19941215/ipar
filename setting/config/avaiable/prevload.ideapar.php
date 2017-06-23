<?php
$dir_base = $this->get('dir.base');
$dir_sites = realpath($dir_base . '/../');

$host_base = 'ideapar.com';

$this
    ->set('debug', false)
    ->set('host', [
        'db' => '127.0.0.1',
        'cache' => '127.0.0.1',

        'www' => 'www.' . $host_base,
        'base' => $host_base,

        'static' => 'static.' . $host_base,
        'prototype' => 'static.' . $host_base,
        'front' => 'static.' .$host_base,
        'api' => 'api.' . $host_base,
        'i' => 'i.' . $host_base
    ])

    ->set('dir', [
        'base' => $dir_base,
        'static' => $dir_sites . '/static/public',
        'prototype' => $dir_sites . '/static/public',
        'front' => $dir_sites . '/static/public',
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

